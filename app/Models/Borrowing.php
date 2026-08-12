<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    use HasFactory;

    public const TYPE_BORROW = 'borrow';
    public const TYPE_REPAY = 'repay';

    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'lender_id',
        'account_id',
        'date_ad',
        'date_bs',
        'financial_year',
        'amount',
        'reference',
        'attachment',
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

    public static function types(): array
    {
        return [
            self::TYPE_BORROW => 'Borrow',
            self::TYPE_REPAY => 'Repay',
        ];
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(
            Lender::class,
            'lender_id'
        );
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            Account::class,
            'account_id'
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