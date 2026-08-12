<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $this->ensureAdmin();

        $staffUsers = User::query()
            ->where('role', 'staff')
            ->with('permissions')
            ->orderBy('name')
            ->paginate(20);

        return view('staff.index', compact('staffUsers'));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('staff.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $staff = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'is_active' => $request->boolean('is_active'),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('staff.edit', $staff)
            ->with('success', 'Staff created successfully.');
    }

    public function edit(User $staff)
    {
        $this->ensureAdmin();

        abort_unless($staff->isStaff(), 404);

        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        $this->ensureAdmin();

        abort_unless($staff->isStaff(), 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($staff->id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->is_active = $request->boolean('is_active');

        if (! empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff updated successfully.');
    }

    public function destroy(User $staff)
    {
        $this->ensureAdmin();

        abort_unless($staff->isStaff(), 404);

        $staff->is_active = false;
        $staff->save();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff deactivated successfully.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->user()?->isAdmin(),
            403
        );
    }
}