<?php

namespace App\Console\Commands;

use App\Models\CustomBudget;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetItem;
use Carbon\Carbon;
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
    public function handle()
    {
        $today = (string) Carbon::now()->day;

        CustomBudgetItem::whereIn(
            'custom_budget_field_id',
            CustomBudgetField::whereIn(
                'custom_budget_id',
                CustomBudget::where('reset_date', $today)->pluck('id')
            )->pluck('id')
        )->update(['item_amount' => 0]);
    }
}
