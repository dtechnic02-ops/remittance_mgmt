<?php

namespace Tests\Feature;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_customer_with_normalized_account_and_synchronized_dates(): void
    {
        $admin = $this->user('admin');
        $englishDate = '2026-08-14';
        $nepaliDate = LaravelNepaliDate::from($englishDate)->toNepaliDate('Y-m-d', 'en');

        $this->actingAs($admin)->post(route('customers.store'), [
            'name' => 'Account Customer',
            'english_date' => $englishDate,
            'nepali_date' => $nepaliDate,
            'account' => '0012-345',
            'branch' => 'Main Branch',
            'account_type' => 'social_security',
        ])->assertRedirect();

        $customer = Customer::firstOrFail();
        $this->assertSame('0012-345', $customer->account);
        $this->assertSame('social_security', $customer->account_type);
        $this->assertSame('Social Security', $customer->accountTypeLabel());
        $this->assertSame($englishDate, $customer->english_date->format('Y-m-d'));
        $this->assertSame($nepaliDate, $customer->nepali_date);
    }

    public function test_authorized_staff_can_create_customer_with_new_fields_and_private_kyc(): void
    {
        Storage::fake('local');
        $staff = $this->user('staff');
        $this->grant($staff, 'customer.create');

        $this->actingAs($staff)->post(route('customers.store'), [
            'name' => 'Staff Customer',
            'nepali_date' => '2083-04-29',
            'account' => '0000456',
            'branch' => 'City Branch',
            'account_type' => 'personal',
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ])->assertRedirect();

        $customer = Customer::firstOrFail();
        $this->assertNotNull($customer->english_date);
        $this->assertSame('0000456', $customer->account);
        Storage::disk('local')->assertExists($customer->photo);
    }

    public function test_invalid_account_type_and_invalid_nepali_date_are_rejected(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->post(route('customers.store'), [
            'name' => 'Invalid Customer',
            'nepali_date' => '2083-99-99',
            'account_type' => 'government',
        ])->assertSessionHasErrors(['nepali_date', 'account_type']);

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_customer_update_changes_new_fields(): void
    {
        $admin = $this->user('admin');
        $customer = $this->customer($admin);
        $englishDate = '2026-08-15';

        $this->actingAs($admin)->put(route('customers.update', $customer), [
            'name' => $customer->name,
            'english_date' => $englishDate,
            'account' => '00999',
            'branch' => 'Updated Branch',
            'account_type' => 'corporate',
        ])->assertRedirect(route('customers.show', $customer));

        $customer->refresh();
        $this->assertSame('00999', $customer->account);
        $this->assertSame('Updated Branch', $customer->branch);
        $this->assertSame('corporate', $customer->account_type);
        $this->assertSame($englishDate, $customer->english_date->format('Y-m-d'));
    }

    public function test_existing_null_customer_loads_and_new_information_renders(): void
    {
        $admin = $this->user('admin');
        $legacy = $this->customer($admin);

        $this->actingAs($admin)->get(route('customers.show', $legacy))
            ->assertOk()->assertSee('Account Type');

        $legacy->update([
            'english_date' => '2026-08-14', 'nepali_date' => '2083-04-29',
            'account' => '00123', 'branch' => 'Central', 'account_type' => 'allowance',
        ]);

        $this->actingAs($admin)->get(route('customers.index'))
            ->assertOk()->assertSee('00123')->assertSee('Allowance')->assertSee('2083-04-29');
        $this->actingAs($admin)->get(route('customers.show', $legacy))
            ->assertOk()->assertSee('Central')->assertSee('Allowance');
    }

    public function test_date_conversion_is_two_way_and_customer_authorization_is_unchanged(): void
    {
        $staff = $this->user('staff');
        $shareholder = $this->user('shareholder');

        $this->actingAs($staff)->get(route('customers.index'))->assertForbidden();
        $this->actingAs($shareholder)->get(route('customers.index'))->assertForbidden();

        $english = '2026-08-14';
        $forward = $this->actingAs($staff)->getJson(route('customers.date-convert', ['english_date' => $english]))
            ->assertOk()->json();
        $this->assertSame($english, $forward['english_date']);

        $this->actingAs($staff)->getJson(route('customers.date-convert', ['nepali_date' => $forward['nepali_date']]))
            ->assertOk()->assertJson(['english_date' => $english, 'nepali_date' => $forward['nepali_date']]);

        $this->actingAs($shareholder)
            ->getJson(route('customers.date-convert', ['english_date' => $english]))
            ->assertForbidden();
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role, 'is_active' => true, 'email_verified_at' => now(),
        ]);
    }

    private function grant(User $user, string $code): void
    {
        $permission = Permission::query()->where('code', $code)->firstOrFail();
        $user->permissions()->attach($permission->id, ['assigned_at' => now()]);
    }

    private function customer(User $user): Customer
    {
        return Customer::create([
            'customer_code' => 'CUS-000001', 'name' => 'Legacy Customer',
            'is_active' => true, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
    }
}
