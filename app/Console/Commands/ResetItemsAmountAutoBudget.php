<?php

namespace App\Console\Commands;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetItem;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reset-items-amount-auto-budget')]
#[Description('Reset auto budget item amounts on reset day, excluding Savings')]
class ResetItemsAmountAutoBudget extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->day;

        $fieldIds = AutoBudgetField::query()
            ->whereIn(
                'auto_budget_id',
                AutoBudget::where('reset_date', $today)->pluck('id')
            )
            ->where('field_name', '!=', 'Savings')
            ->pluck('id');

        if ($fieldIds->isEmpty()) {
            return self::SUCCESS;
        }

        AutoBudgetItem::whereIn('auto_budget_field_id', $fieldIds)
            ->where('field_name', '!=', 'Savings')
            ->update(['item_amount' => 0]);

        return self::SUCCESS;
    }
}
