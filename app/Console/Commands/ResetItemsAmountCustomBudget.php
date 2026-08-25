<?php

namespace App\Console\Commands;

use App\Models\CustomBudget;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetFieldNonResetable;
use App\Models\CustomBudgetItem;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reset-items-amount-custom-budget')]
#[Description('Reset custom budget item amounts on reset day, excluding non-resetable fields')]
class ResetItemsAmountCustomBudget extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->day;

        $nonResetableFieldIds = CustomBudgetFieldNonResetable::pluck('custom_budget_field_id');

        $fieldIds = CustomBudgetField::query()
            ->whereIn(
                'custom_budget_id',
                CustomBudget::where('reset_date', $today)->pluck('id')
            )
            ->when(
                $nonResetableFieldIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $nonResetableFieldIds)
            )
            ->pluck('id');

        if ($fieldIds->isEmpty()) {
            return self::SUCCESS;
        }

        CustomBudgetItem::whereIn('custom_budget_field_id', $fieldIds)
            ->update(['item_amount' => 0]);

        return self::SUCCESS;
    }
}
