<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BudgetResetDate
{
    public static function isResetDay(int $resetDate, ?Carbon $today = null): bool
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        if ($today->day === $resetDate) {
            return true;
        }

        if ($today->day !== 1) {
            return false;
        }

        return $resetDate > $today->copy()->subDay()->daysInMonth;
    }

    public static function nextOccurrence(int $resetDate, ?Carbon $from = null): Carbon
    {
        $from = ($from ?? Carbon::now())->copy()->startOfDay();

        for ($offset = 0; $offset < 3; $offset++) {
            $month = $from->copy()->startOfMonth()->addMonths($offset);
            $daysInMonth = $month->daysInMonth;

            $candidate = $resetDate <= $daysInMonth
                ? $month->copy()->day($resetDate)
                : $month->copy()->addMonth()->startOfMonth();

            if ($candidate->gt($from)) {
                return $candidate;
            }
        }

        return $from->copy()->addMonth()->startOfMonth();
    }

    public static function constrainDueToday(Builder $query, ?Carbon $today = null): Builder
    {
        $today = ($today ?? Carbon::today())->copy()->startOfDay();

        return $query->where(function (Builder $inner) use ($today) {
            $inner->where('reset_date', $today->day);

            if ($today->day === 1) {
                $inner->orWhere('reset_date', '>', $today->copy()->subDay()->daysInMonth);
            }
        });
    }
}
