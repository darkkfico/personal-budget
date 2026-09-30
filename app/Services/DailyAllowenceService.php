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
        session()->forget([
            'amount_left',
            'deleted_amount',
            'last_day',
            'allowance_on',
            'today_allowance',
            'items_baseline',
            'daily_allowence_warned_on',
        ]);
    }

    private function calculate(float $spendableBudget, float $itemsAmount, string $currency, int $resetDate): array
    {
        $givenDate = BudgetResetDate::nextOccurrence($resetDate);
        $daysDiff = max(1, (int) Carbon::today()->startOfDay()->diffInDays($givenDate, true));
        $todayAllowance = $this->roundDaily(($spendableBudget - $itemsAmount) / $daysDiff, $currency);
        $today = Carbon::today()->toDateString();

        $frozen = session('allowance_on') === $today
            && session()->exists('today_allowance')
            && session()->exists('items_baseline');

        if (! $frozen) {
            session([
                'allowance_on' => $today,
                'today_allowance' => $todayAllowance,
                'items_baseline' => $itemsAmount,
            ]);
            $daily = $todayAllowance;
        } else {
            $daily = session('today_allowance') - ($itemsAmount - (float) session('items_baseline'));
        }

        if ($daily > 0) {
            session()->forget('daily_allowence_warned_on');
        }

        return [
            'sub1' => $daily,
            'daysDiff' => $daysDiff,
            'warnDaily' => $daily <= 0 && session('daily_allowence_warned_on') !== $today,
        ];
    }

    public function acknowledgeWarning(): void
    {
        session(['daily_allowence_warned_on' => Carbon::today()->toDateString()]);
    }

    private function roundDaily(float $amount, string $currency): float
    {
        if ($currency === 'MKD') {
            return (float) round($amount);
        }

        return round($amount, 2);
    }
}
