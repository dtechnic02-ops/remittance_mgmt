<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningBalanceAdjustment extends Model
{
    protected $fillable = [
        'opening_balance_id',
        'account_id',
        'action',
        'opening_balance_before',
        'opening_balance_after',
        'current_balance_before',
        'current_balance_after',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance_before' => 'integer',
            'opening_balance_after' => 'integer',
            'current_balance_before' => 'integer',
            'current_balance_after' => 'integer',
        ];
    }
}
