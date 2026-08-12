<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpeningBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'date_ad',
        'date_bs',
        'financial_year',
        'amount',
        'note',
        'attachment',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date_ad' => 'date',
            'amount' => 'integer',
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

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}