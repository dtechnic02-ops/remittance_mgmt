<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

  public const TYPE_CASH = 'cash';
public const TYPE_BANK = 'bank';
public const TYPE_REMITTANCE = 'remittance';
public const TYPE_BLB = 'blb';
public const TYPE_COMMISSION = 'commission';
public const TYPE_EXPENSE = 'expense';
public const TYPE_FIXED_DEPOSIT = 'fixed_deposit';
public const TYPE_OTHER = 'other';

    protected $fillable = [
        'name',
        'code',
        'type',
        'account_number',
        'branch_name',
        'opening_balance',
        'current_balance',
        'opening_date',
        'opening_date_bs',
        'allow_negative',
        'is_active',
        'note',
        'attachment',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'integer',
            'current_balance' => 'integer',
            'opening_date' => 'date',
            'allow_negative' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

 public static function types(): array
{
    return [
        self::TYPE_CASH => 'Cash',
        self::TYPE_BANK => 'Bank',
        self::TYPE_REMITTANCE => 'Remittance Provider',
        self::TYPE_BLB => 'BLB',
        self::TYPE_FIXED_DEPOSIT => 'Fixed Deposit',
        self::TYPE_OTHER => 'Other',
    ];
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