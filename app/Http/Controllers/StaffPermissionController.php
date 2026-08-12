<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class StaffPermissionController extends Controller
{
    public function index()
    {
        $this->ensureAdmin();

        $staffUsers = User::query()
            ->where('role', 'staff')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('staff-permissions.index', compact('staffUsers'));
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();

        abort_unless($user->isStaff(), 404);

        $permissions = Permission::query()
            ->where('is_active', true)
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        $assignedPermissionIds = $user->permissions()
            ->pluck('permissions.id')
            ->all();

        return view(
            'staff-permissions.edit',
            compact(
                'user',
                'permissions',
                'assignedPermissionIds'
            )
        );
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        abort_unless($user->isStaff(), 404);

        $validated = $request->validate([
            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $permissionIds = collect(
            $validated['permissions'] ?? []
        )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $validPermissionIds = Permission::query()
            ->where('is_active', true)
            ->whereIn('id', $permissionIds)
            ->pluck('id');

        $syncData = [];

        foreach ($validPermissionIds as $permissionId) {
            $syncData[$permissionId] = [
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ];
        }

        $user->permissions()->sync($syncData);

        return redirect()
            ->route('staff-permissions.index')
            ->with(
                'success',
                'Staff permissions updated successfully.'
            );
    }

    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->user()?->isAdmin(),
            403
        );
    }
}