<?php

namespace App\Services;

use App\Models\AutoBudget;
use App\Models\CustomBudget;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DailyAllowenceService
{
    public function forAuto(AutoBudget $budget, Collection $items): array
    {
        $itemsAmount = $items->where('field_name', '!=', 'Savings')->sum('item_amount');
        $spendable = $budget->budget_amount * 0.8;

        return $this->calculate($spendable, $itemsAmount, $budget->currency, (int) $budget->reset_date);
    }

    public function forCustom(CustomBudget $budget, Collection $items, Collection $nonResetableFieldIds): array
    {
        $itemsAmount = $items->whereNotIn('custom_budget_field_id', $nonResetableFieldIds)->sum('item_amount');

        return $this->calculate($budget->budget_amount, $itemsAmount, $budget->currency, (int) $budget->reset_date);
    }

    public function clear(): void
    {
        session()->forget('amount_left');
        session()->forget('deleted_amount');
        session()->forget('last_day');
        session()->forget('daily_allowence_warned_on');
    }

    private function calculate(float $spendableBudget, float $itemsAmount, string $currency, int $resetDate): array
    {
        $givenDate = BudgetResetDate::nextOccurrence($resetDate);

        $daysDiff = Carbon::now()->diffInDays($givenDate);

        if ($currency == 'MKD') {
            $daily = round(($spendableBudget - $itemsAmount) / $daysDiff);
        } else {
            $daily = round(($spendableBudget - $itemsAmount) / $daysDiff, 2);
        }

        if (session('amount_left')) {
            if (session('deleted_amount')) {
                session(['amount_left' => session('amount_left') + session('deleted_amount')]);
                session()->forget('deleted_amount');
                $daily = session('amount_left') - $itemsAmount;
            } elseif (session('last_day')) {
                if (session('last_day') != Carbon::now()->day) {
                    session(['last_day' => Carbon::now()->day]);
                    session(['amount_left' => $itemsAmount + session('amount_left')]);
                    $daily = session('amount_left') - $itemsAmount;
                } else {
                    $daily = session('amount_left') - $itemsAmount;
                }
            }
        } else {
            session(['amount_left' => $daily]);
            session(['last_day' => Carbon::now()->day]);
        }

        if ($daily > 0) {
            session()->forget('daily_allowence_warned_on');
        }

        return [
            'sub1' => $daily,
            'daysDiff' => $daysDiff,
            'warnDaily' => $daily <= 0 && session('daily_allowence_warned_on') !== Carbon::now()->toDateString(),
        ];
    }

    public function acknowledgeWarning(): void
    {
        session(['daily_allowence_warned_on' => Carbon::now()->toDateString()]);
    }
}
