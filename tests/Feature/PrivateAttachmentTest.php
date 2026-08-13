<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\Permission;
use App\Models\Shareholder;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivateAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_upload_is_private_and_admin_can_view_it(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $admin = $this->user('admin');

        $response = $this->actingAs($admin)->post(route('customers.store'), [
            'name' => 'Private Customer',
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $customer = Customer::firstOrFail();
        $response->assertRedirect(route('customers.show', $customer));
        Storage::disk('local')->assertExists($customer->photo);
        Storage::disk('public')->assertMissing($customer->photo);
        $this->actingAs($admin)
            ->get(route('customers.documents.type', [$customer, 'photo']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_customer_document_requires_authentication_and_view_permission(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');
        $customer = Customer::create([
            'customer_code' => 'CUS-001', 'name' => 'Customer',
            'photo' => UploadedFile::fake()->image('photo.jpg')->store('customers/photos', 'local'),
            'is_active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $this->get(route('customers.documents.type', [$customer, 'photo']))
            ->assertRedirect(route('login'));

        $staff = $this->user('staff');
        $this->actingAs($staff)
            ->get(route('customers.documents.type', [$customer, 'photo']))
            ->assertForbidden();

        $this->grant($staff, 'customer.view', $admin);
        $this->actingAs($staff)
            ->get(route('customers.documents.type', [$customer, 'photo']))
            ->assertOk();
    }

    public function test_shareholder_can_view_only_own_kyc(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');
        $owner = $this->user('shareholder');
        $other = $this->user('shareholder');
        $own = $this->shareholder($admin, $owner, 'SH-001');
        $another = $this->shareholder($admin, $other, 'SH-002');

        $this->actingAs($owner)
            ->get(route('shareholder.documents.show', [$own, 'citizenship-front']))
            ->assertOk();
        $this->actingAs($owner)
            ->get(route('shareholder.documents.show', [$another, 'citizenship-front']))
            ->assertForbidden();
    }

    public function test_invalid_type_traversal_and_missing_file_fail_safely(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');
        $customer = Customer::create([
            'customer_code' => 'CUS-001', 'name' => 'Customer',
            'photo' => 'customers/photos/missing.jpg', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('customers.documents.type', [$customer, '..']))
            ->assertNotFound();
        $this->actingAs($admin)
            ->get(route('customers.documents.type', [$customer, 'photo']))
            ->assertNotFound();
    }

    public function test_invalid_and_oversized_uploads_are_rejected(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');

        $this->actingAs($admin)->post(route('customers.store'), [
            'name' => 'Invalid',
            'photo' => UploadedFile::fake()->create('payload.php', 1, 'application/x-php'),
        ])->assertSessionHasErrors('photo');

        $this->actingAs($admin)->post(route('customers.store'), [
            'name' => 'Too Large',
            'citizenship_front' => UploadedFile::fake()->image('large.jpg')->size(5121),
        ])->assertSessionHasErrors('citizenship_front');
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_failed_shareholder_update_keeps_old_file_and_removes_replacement(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');
        $shareholder = $this->shareholder($admin, null, 'SH-001');
        $old = UploadedFile::fake()->image('old.jpg')->store('shareholders/photos', 'local');
        $shareholder->update(['photo' => $old]);

        Shareholder::updating(fn () => throw new \RuntimeException('Failed update'));

        $this->withoutExceptionHandling();
        try {
            $this->actingAs($admin)->put(route('shareholders.update', $shareholder), [
                'code' => 'SH-001', 'name' => 'Owner', 'is_active' => '1',
                'photo' => UploadedFile::fake()->image('new.jpg'),
            ]);
            $this->fail('Expected update failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Failed update', $exception->getMessage());
        }

        Storage::disk('local')->assertExists($old);
        $this->assertCount(1, Storage::disk('local')->allFiles('shareholders/photos'));
    }

    public function test_failed_create_removes_new_private_file(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');
        $category = ExpenseCategory::create([
            'name' => 'Office', 'code' => 'OFF', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $account = Account::create([
            'name' => 'Cash', 'code' => 'CASH', 'type' => Account::TYPE_CASH,
            'opening_balance' => 0, 'current_balance' => 0, 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $service = \Mockery::mock(ExpenseService::class);
        $service->shouldReceive('create')->once()->andThrow(new \RuntimeException('Business failure'));
        $this->app->instance(ExpenseService::class, $service);

        $this->actingAs($admin)->post(route('expenses.store'), [
            'expense_category_id' => $category->id, 'account_id' => $account->id,
            'date_ad' => '2026-08-13', 'date_bs' => '2083-04-29',
            'financial_year' => '2083/84', 'amount' => 1,
            'attachment' => UploadedFile::fake()->create('evidence.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('transaction');

        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true]);
    }

    private function grant(User $staff, string $code, User $admin): void
    {
        $permission = Permission::firstOrCreate(
            ['code' => $code],
            ['name' => $code, 'is_active' => true]
        );
        $staff->permissions()->attach($permission->id, [
            'assigned_by' => $admin->id, 'assigned_at' => now(),
        ]);
    }

    private function shareholder(User $admin, ?User $user, string $code): Shareholder
    {
        $path = UploadedFile::fake()->image($code.'.jpg')
            ->store('shareholders/citizenship', 'local');

        return Shareholder::create([
            'user_id' => $user?->id, 'code' => $code, 'name' => 'Owner',
            'kitta' => 0, 'per_kitta_value' => 1000, 'total_investment' => 0,
            'citizenship_front' => $path, 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
    }
}
