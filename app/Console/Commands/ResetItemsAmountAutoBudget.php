<?php

namespace App\Console\Commands;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetItem;
use App\Services\BudgetResetDate;
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
    public function handle()
    {
        $fieldIds = AutoBudgetField::whereIn(
            'auto_budget_id',
            BudgetResetDate::constrainDueToday(AutoBudget::query())->pluck('id')
        )->where('field_name', '!=', 'Savings')->pluck('id');

        AutoBudgetItem::whereIn('auto_budget_field_id', $fieldIds)->update(['item_amount' => 0]);
    }
}
