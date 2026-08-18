<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use RuntimeException;

class ShareholderAccountController extends Controller
{
    public function edit(Shareholder $shareholder)
    {
        $this->ensureAdmin();

        $user = $shareholder->user;

        return view(
            'shareholder-accounts.edit',
            compact('shareholder', 'user')
        );
    }

    public function update(
        Request $request,
        Shareholder $shareholder
    ) {
        $this->ensureAdmin();

        $linkedUser = $shareholder->user;

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->ignore($linkedUser?->id),
            ],

            'password' => [
                $linkedUser ? 'nullable' : 'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $shareholder,
            $linkedUser,
            $validated
        ) {
            if ($linkedUser) {
                if (! $linkedUser->isShareholder()) {
                    throw new RuntimeException(
                        'Linked user is not a shareholder account.'
                    );
                }

                $linkedUser->name = $shareholder->name;
                $linkedUser->email = $validated['email'];
                $linkedUser->is_active =
                    $request->boolean('is_active');

                if (! empty($validated['password'])) {
                    $linkedUser->password =
                        Hash::make($validated['password']);
                }

                $linkedUser->save();

                return;
            }

            $user = User::create([
                'name' => $shareholder->name,
                'email' => $validated['email'],
                'password' => Hash::make(
                    $validated['password']
                ),
                'role' => 'shareholder',
                'is_active' =>
                    $request->boolean('is_active'),
                'email_verified_at' => now(),
            ]);

            $shareholder->user_id = $user->id;
            $shareholder->updated_by = auth()->id();
            $shareholder->save();
        });

        return redirect()
            ->route(
                'shareholder-accounts.edit',
                $shareholder
            )
            ->with(
                'success',
                'Shareholder login account saved successfully.'
            );
    }

    public function deactivate(
        Shareholder $shareholder
    ) {
        $this->ensureAdmin();

        $user = $shareholder->user;

        abort_unless($user, 404);

        abort_unless(
            $user->isShareholder(),
            404
        );

        $user->is_active = false;
        $user->save();

        return redirect()
            ->route(
                'shareholder-accounts.edit',
                $shareholder
            )
            ->with(
                'success',
                'Shareholder login account deactivated successfully.'
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