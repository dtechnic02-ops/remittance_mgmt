<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareTransaction extends Model
{
    use HasFactory;

    public const TYPE_BUY = 'buy';
    public const TYPE_WITHDRAW = 'withdraw';
    public const TYPE_TRANSFER = 'transfer';

    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'shareholder_id',
        'to_shareholder_id',
        'account_id',
        'date_ad',
        'date_bs',
        'financial_year',
        'kitta',
        'per_kitta_value',
        'total_amount',
        'investment_effect',
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
            'kitta' => 'integer',
            'per_kitta_value' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'investment_effect' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_BUY => 'Buy Kitta',
            self::TYPE_WITHDRAW => 'Withdraw Share',
            self::TYPE_TRANSFER => 'Share Transfer',
        ];
    }

    public function shareholder(): BelongsTo
    {
        return $this->belongsTo(
            Shareholder::class,
            'shareholder_id'
        );
    }

    public function toShareholder(): BelongsTo
    {
        return $this->belongsTo(
            Shareholder::class,
            'to_shareholder_id'
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
