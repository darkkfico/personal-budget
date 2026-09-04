<?php

namespace App\Console\Commands;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetItem;
use App\Services\BudgetResetDate;
use App\Services\ResetCarryoverService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reset-items-amount-auto-budget')]
#[Description('Command description')]
class ResetItemsAmountAutoBudget extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ResetCarryoverService $carryover)
    {
        $budgets = BudgetResetDate::constrainDueToday(AutoBudget::query())->get();

        foreach ($budgets as $budget) {
            $carryover->captureLeftover($budget);
        }

        $fieldIds = AutoBudgetField::whereIn(
            'auto_budget_id',
            $budgets->pluck('id')
        )->where('field_name', '!=', 'Savings')->pluck('id');

        AutoBudgetItem::whereIn('auto_budget_field_id', $fieldIds)->update(['item_amount' => 0]);
    }
}
