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

    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'shareholder_id',
        'account_id',
        'date_ad',
        'date_bs',
        'financial_year',
        'kitta',
        'per_kitta_value',
        'total_amount',
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
            'per_kitta_value' => 'integer',
            'total_amount' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_BUY => 'Buy Kitta',
            self::TYPE_WITHDRAW => 'Withdraw Share',
        ];
    }

    public function shareholder(): BelongsTo
    {
        return $this->belongsTo(
            Shareholder::class,
            'shareholder_id'
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