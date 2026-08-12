<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Permission;
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'group',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'assigned_by',
                'assigned_at',
            ])
            ->withTimestamps();
    }
public function permissions()
{
    return $this->belongsToMany(Permission::class)
        ->withPivot([
            'assigned_by',
            'assigned_at',
        ])
        ->withTimestamps();
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

public function givePermission(string $code, ?int $assignedBy = null): void
{
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
}