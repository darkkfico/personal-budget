<?php

namespace App\Support;

class Currency
{
    public static function format(mixed $amount, ?string $code): string
    {
        $code = strtoupper((string) $code);

        return match ($code) {
            'EUR' => '€'.$amount,
            'USD' => '$'.$amount,
            default => trim($amount.' '.$code),
        };
    }
}
