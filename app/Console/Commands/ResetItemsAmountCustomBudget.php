<?php

namespace App\Console\Commands;

use App\Models\CustomBudget;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetFieldNonResetable;
use App\Models\CustomBudgetItem;
use App\Services\BudgetResetDate;
use App\Services\ResetCarryoverService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reset-items-amount-custom-budget')]
#[Description('Command description')]
class ResetItemsAmountCustomBudget extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ResetCarryoverService $carryover)
    {
        $budgets = BudgetResetDate::constrainDueToday(CustomBudget::query())->get();

        foreach ($budgets as $budget) {
            $carryover->captureLeftover($budget);
        }

        $fieldIds = CustomBudgetField::whereIn(
            'custom_budget_id',
            $budgets->pluck('id')
        )->pluck('id');

        $skipIds = CustomBudgetFieldNonResetable::whereIn('custom_budget_field_id', $fieldIds)
            ->pluck('custom_budget_field_id');

        CustomBudgetItem::whereIn('custom_budget_field_id', $fieldIds->diff($skipIds))
            ->update(['item_amount' => 0]);
    }
}
