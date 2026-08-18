<?php

namespace App\Services;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Carbon\Carbon;

class FinancialDateService
{
    public function fromEnglishDate(string $date): array
    {
        $dateAd = Carbon::parse($date)->toDateString();
        $dateBs = LaravelNepaliDate::from($dateAd)->toNepaliDate('Y-m-d', 'en');

        return [
            'date_ad' => $dateAd,
            'date_bs' => $dateBs,
            'financial_year' => $this->financialYear($dateBs),
        ];
    }

    public function financialYear(string $dateBs): string
    {
        [$year, $month] = array_map('intval', explode('-', $dateBs));
        $startYear = $month >= 4 ? $year : $year - 1;

        return $startYear.'/'.substr((string) ($startYear + 1), -2);
    }
}
