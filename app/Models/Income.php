<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'income_number',
        'income_category_id',
        'account_id',
        'date_ad',
        'date_bs',
        'financial_year',
        'amount',
        'reference',
        'note',
        'attachment',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            IncomeCategory::class,
            'income_category_id'
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