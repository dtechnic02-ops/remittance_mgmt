<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    public const ACCOUNT_TYPES = [
        'personal' => 'Personal',
        'social_security' => 'Social Security',
        'allowance' => 'Allowance',
        'institution' => 'Institution',
        'corporate' => 'Corporate',
        'other' => 'Other',
    ];

    protected $fillable = [
        'customer_code',
        'name',
        'english_date',
        'nepali_date',
        'mobile',
        'phone',
        'email',
        'address',
        'account',
        'branch',
        'account_type',
        'citizenship_number',
        'photo',
        'citizenship_front',
        'citizenship_back',
        'is_active',
        'note',
        'created_by',
        'updated_by',
        'deactivated_by',
        'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'english_date' => 'date',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }

    public function accountTypeLabel(): ?string
    {
        return self::ACCOUNT_TYPES[$this->account_type] ?? null;
    }

    public function otherDocuments(): HasMany
    {
        return $this->hasMany(CustomerOtherDocument::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deactivator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }
}
