<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shareholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function shareTransactions(): HasMany
    {
        return $this->hasMany(
            ShareTransaction::class,
            'shareholder_id'
        );
    }

    public function receivedShareTransfers(): HasMany
    {
        return $this->hasMany(
            ShareTransaction::class,
            'to_shareholder_id'
        );
    }

    public function hasFinancialHistory(): bool
    {
        return $this->shareTransactions()->exists()
            || $this->receivedShareTransfers()->exists();
    }

    public function canHardDelete(): bool
    {
        return ! $this->hasFinancialHistory()
            && (int) $this->kitta === 0
            && (int) $this->total_investment === 0;
    }
}