<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shareholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'mobile',
        'email',
        'address',
        'kitta',
        'per_kitta_value',
        'total_investment',
        'type',
        'is_active',
        'photo',
        'citizenship_front',
        'citizenship_back',
        'other_document',
        'note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'kitta' => 'integer',
            'per_kitta_value' => 'integer',
            'total_investment' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}