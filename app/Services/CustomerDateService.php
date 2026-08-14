<?php

namespace App\Services;

use Anuzpandey\LaravelNepaliDate\Exceptions\InvalidDateException;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Illuminate\Validation\ValidationException;

class CustomerDateService
{
    public function synchronize(?string $englishDate, ?string $nepaliDate): array
    {
        $englishDate = filled($englishDate) ? trim($englishDate) : null;
        $nepaliDate = filled($nepaliDate) ? trim($nepaliDate) : null;

        try {
            if ($englishDate) {
                if (! LaravelNepaliDate::validateEnglish($englishDate)) {
                    throw ValidationException::withMessages(['english_date' => 'The English Date is invalid or unsupported.']);
                }
                $converted = LaravelNepaliDate::from($englishDate)->toNepaliDate('Y-m-d', 'en');
                if ($nepaliDate && $nepaliDate !== $converted) {
                    throw ValidationException::withMessages(['nepali_date' => 'The Nepali Date does not match the English Date.']);
                }
                return ['english_date' => $englishDate, 'nepali_date' => $converted];
            }

            if ($nepaliDate) {
                if (! LaravelNepaliDate::validateNepali($nepaliDate)) {
                    throw ValidationException::withMessages(['nepali_date' => 'The Nepali Date is invalid or unsupported.']);
                }
                return [
                    'english_date' => LaravelNepaliDate::from($nepaliDate)->toEnglishDate('Y-m-d', 'en'),
                    'nepali_date' => $nepaliDate,
                ];
            }
        } catch (InvalidDateException) {
            throw ValidationException::withMessages(['nepali_date' => 'The supplied English or Nepali date is invalid or unsupported.']);
        }

        return ['english_date' => null, 'nepali_date' => null];
    }
}
