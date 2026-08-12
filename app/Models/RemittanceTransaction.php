<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemittanceTransaction extends Model
{
    use HasFactory;

  protected $fillable = [
    'transaction_number',
    'direction',
    'customer_id',
    'provider_account_id',
    'cash_account_id',
    'date_ad',
    'date_bs',
    'financial_year',
    'principal_amount',
    'service_charge',
    'total_cash_received',
    'provider_reference',
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
            'principal_amount' => 'integer',
            'service_charge' => 'integer',
            'total_cash_received' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'provider_account_id');
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}