<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'transaction_type',
        'transaction_id',
        'transaction_number',
        'date_ad',
        'date_bs',
        'financial_year',
        'direction',
        'amount',
        'balance_after',
        'component',
        'note',
        'created_by',
        'is_reversal',
        'reversal_of_id',
    ];

    protected function casts(): array
    {
        return [
            'date_ad' => 'date',
            'amount' => 'integer',
            'balance_after' => 'integer',
            'is_reversal' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(
            LedgerEntry::class,
            'reversal_of_id'
        );
    }
}