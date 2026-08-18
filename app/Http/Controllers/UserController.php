<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
                in_array($role, ['staff', 'shareholder'], true),
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