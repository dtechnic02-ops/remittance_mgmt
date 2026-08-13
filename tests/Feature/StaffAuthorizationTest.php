<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_operational_routes_without_assignments(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get(route('remittances.index'))->assertOk();
        $this->actingAs($admin)->get(route('expenses.create'))->assertOk();
        $this->actingAs($admin)->get(route('ledger.index'))->assertOk();
    }

    public function test_staff_without_permission_is_denied_direct_access(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)->get(route('remittances.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('remittances.create'))->assertForbidden();
        $this->actingAs($staff)->post(route('remittances.store'), [])->assertForbidden();
        $this->actingAs($staff)
            ->post('/remittances/1/cancel', ['cancellation_reason' => 'No access'])
            ->assertForbidden();
        $this->actingAs($staff)->get(route('ledger.index'))->assertForbidden();
    }

    public function test_partial_staff_permission_allows_only_that_operation(): void
    {
        $staff = $this->user('staff');
        $this->grant($staff, 'remittance.view');

        $this->actingAs($staff)->get(route('remittances.index'))->assertOk();
        $this->actingAs($staff)->get(route('remittances.create'))->assertForbidden();
        $this->actingAs($staff)->post(route('remittances.store'), [])->assertForbidden();
        $this->actingAs($staff)
            ->post('/remittances/1/cancel', ['cancellation_reason' => 'No access'])
            ->assertForbidden();
    }

    public function test_revoked_permission_takes_effect_on_next_request(): void
    {
        $staff = $this->user('staff');
        $permission = $this->grant($staff, 'ledger.view');

        $this->actingAs($staff)->get(route('ledger.index'))->assertOk();
        $staff->permissions()->detach($permission->id);
        $this->actingAs($staff)->get(route('ledger.index'))->assertForbidden();
    }

    public function test_shareholder_cannot_access_operational_routes(): void
    {
        $shareholder = $this->user('shareholder');

        $this->actingAs($shareholder)->get(route('remittances.index'))->assertForbidden();
        $this->actingAs($shareholder)->get(route('customers.index'))->assertForbidden();
        $this->actingAs($shareholder)->get(route('ledger.index'))->assertForbidden();
    }

    public function test_only_admin_can_manage_staff_permissions(): void
    {
        $staff = $this->user('staff');
        $otherStaff = $this->user('staff');
        $shareholder = $this->user('shareholder');
        $permission = Permission::where('code', 'ledger.view')->firstOrFail();
        $payload = ['permissions' => [$permission->id]];

        $this->actingAs($staff)
            ->put(route('staff-permissions.update', $staff), $payload)
            ->assertForbidden();
        $this->actingAs($staff)
            ->put(route('staff-permissions.update', $otherStaff), $payload)
            ->assertForbidden();
        $this->actingAs($shareholder)
            ->put(route('staff-permissions.update', $otherStaff), $payload)
            ->assertForbidden();

        $admin = $this->user('admin');
        $this->actingAs($admin)
            ->put(route('staff-permissions.update', $otherStaff), $payload)
            ->assertRedirect(route('staff-permissions.index'));
        $this->assertTrue($otherStaff->fresh()->hasPermission('ledger.view'));
    }

    public function test_invalid_permission_id_is_rejected(): void
    {
        $admin = $this->user('admin');
        $staff = $this->user('staff');

        $this->actingAs($admin)
            ->put(route('staff-permissions.update', $staff), [
                'permissions' => [999999],
            ])
            ->assertSessionHasErrors('permissions.0');
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function grant(User $user, string $code): Permission
    {
        $permission = Permission::where('code', $code)->firstOrFail();
        $user->permissions()->attach($permission->id, [
            'assigned_at' => now(),
        ]);

        return $permission;
    }
}
