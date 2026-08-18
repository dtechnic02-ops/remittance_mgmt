<?php

namespace App\Services;

class TransactionNumberService
{
    public static function temporary(): string
    {
        return 'TMP-'.bin2hex(random_bytes(10));
    }

    public static function fromId(string $prefix, int $id): string
    {
        return $prefix.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }
}
