<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'is_active',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isShareholder(): bool
    {
        return $this->role === 'shareholder';
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot([
                'assigned_by',
                'assigned_at',
            ])
            ->withTimestamps();
    }

    public function shareholder(): HasOne
    {
        return $this->hasOne(Shareholder::class);
    }

    public function hasPermission(string $code): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions()
            ->where('code', $code)
            ->where('is_active', true)
            ->exists();
    }

    public function givePermission(
        string $code,
        ?int $assignedBy = null
    ): void {
        $permission = Permission::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        $this->permissions()->syncWithoutDetaching([
            $permission->id => [
                'assigned_by' => $assignedBy,
                'assigned_at' => now(),
            ],
        ]);
    }

    public function revokePermission(string $code): void
    {
        $permission = Permission::query()
            ->where('code', $code)
            ->first();

        if (! $permission) {
            return;
        }

        $this->permissions()->detach($permission->id);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
