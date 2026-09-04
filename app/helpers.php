<?php

use App\Support\Currency;

if (! function_exists('money')) {
    function money(mixed $amount, ?string $currency): string
    {
        return Currency::format($amount, $currency);
    }
}
