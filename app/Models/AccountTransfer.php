<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'from_account_id',
        'to_account_id',
        'date_ad',
        'date_bs',
        'financial_year',
        'amount',
        'reference',
        'note',
        'status',
        'created_by',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'date_ad' => 'date',
            'amount' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'from_account_id'
        );
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'to_account_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by'
        );
    }
}