<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $search = trim((string) $request->get('search'));
        $role = trim((string) $request->get('role'));
        $status = trim((string) $request->get('status'));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($role, ['staff', 'shareholder', 'help_desk'], true),
                fn ($query) => $query->where('role', $role)
            )
            ->when(
                $status === 'active',
                fn ($query) => $query->where('is_active', true)
            )
            ->when(
                $status === 'blocked',
                fn ($query) => $query->where('is_active', false)
            )
            ->where('role', '!=', 'admin')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'users.index',
            compact(
                'users',
                'search',
                'role',
                'status'
            )
        );
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in(['staff', 'shareholder', 'help_desk'])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('users.edit', $user)
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $this->ensureAdmin();
        abort_if($user->isAdmin(), 404);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();
        abort_if($user->isAdmin(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_active = $request->boolean('is_active');

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function block(User $user)
    {
        $this->ensureAdmin();

        abort_if(
            $user->isAdmin(),
            403,
            'Admin account cannot be blocked.'
        );

        abort_if(
            $user->id === auth()->id(),
            403,
            'You cannot block your own account.'
        );

        $user->is_active = false;
        $user->save();

        return back()->with(
            'success',
            'User blocked successfully.'
        );
    }

    public function unblock(User $user)
    {
        $this->ensureAdmin();

        abort_if(
            $user->isAdmin(),
            403,
            'Admin account cannot be modified here.'
        );

        $user->is_active = true;
        $user->save();

        return back()->with(
            'success',
            'User unblocked successfully.'
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
