<?php

namespace App\Services;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetFieldSnapshot;
use App\Models\AutoBudgetSnapshot;
use App\Models\CustomBudget;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetFieldNonResetable;
use App\Models\CustomBudgetFieldSnapshot;
use App\Models\CustomBudgetSnapshot;
use Carbon\Carbon;

class ResetCarryoverService
{
    private const AUTO_FIELDS = [
        'Groceries' => 0.50,
        'Wishes' => 0.30,
        'Savings' => 0.20,
    ];

    public function autoPrompt(AutoBudget $budget): ?array
    {
        if (! $this->shouldPrompt($budget)) {
            return null;
        }

        $field = $budget->autoBudgetFields->firstWhere('field_name', 'Savings');

        if (! $field) {
            return null;
        }

        return [
            'field_name' => $field->field_name,
            'amount' => $field->autoBudgetItems()->sum('item_amount'),
            'budget_amount' => $budget->budget_amount,
            'currency' => $budget->currency,
        ];
    }

    public function customPrompt(CustomBudget $budget): ?array
    {
        if (! $this->shouldPrompt($budget)) {
            return null;
        }

        $fields = $this->customNonResetableFields($budget);

        if ($fields->isEmpty()) {
            return null;
        }

        $amount = $fields->sum(fn ($field) => $field->customBudgetItems()->sum('item_amount'));

        return [
            'field_name' => $fields->pluck('field_name')->join(', '),
            'amount' => $amount,
            'budget_amount' => $budget->budget_amount,
            'currency' => $budget->currency,
        ];
    }

    public function applyAuto(AutoBudget $budget, bool $apply): void
    {
        if (! $this->shouldPrompt($budget)) {
            return;
        }

        if ($apply) {
            $field = $budget->autoBudgetFields()->where('field_name', 'Savings')->first();
            $amount = $field?->autoBudgetItems()->sum('item_amount') ?? 0;
            $newBudget = max(0, $budget->budget_amount - $amount);

            $budget->update([
                'budget_amount' => $newBudget,
                'reset_carry_answered_on' => Carbon::today(),
            ]);

            foreach (self::AUTO_FIELDS as $fieldName => $percentage) {
                AutoBudgetField::where('auto_budget_id', $budget->id)
                    ->where('field_name', $fieldName)
                    ->update(['field_amount' => $newBudget * $percentage]);
            }

            $snapshot = AutoBudgetSnapshot::where('user_id', $budget->user_id)
                ->where('month', Carbon::now()->format('n'))
                ->orderByDesc('id')
                ->first();

            if ($snapshot) {
                $snapshot->update(['budget_amount' => $newBudget]);

                foreach (self::AUTO_FIELDS as $fieldName => $percentage) {
                    AutoBudgetFieldSnapshot::where('auto_budget_snapshot_id', $snapshot->id)
                        ->where('field_name', $fieldName)
                        ->update(['field_amount' => $newBudget * $percentage]);
                }
            }
        } else {
            $budget->update(['reset_carry_answered_on' => Carbon::today()]);
        }
    }

    public function applyCustom(CustomBudget $budget, bool $apply): void
    {
        if (! $this->shouldPrompt($budget)) {
            return;
        }

        if ($apply) {
            $carryFields = $this->customNonResetableFields($budget);
            $amount = $carryFields->sum(fn ($field) => $field->customBudgetItems()->sum('item_amount'));
            $oldBudget = $budget->budget_amount;
            $newBudget = max(0, $oldBudget - $amount);

            $budget->update([
                'budget_amount' => $newBudget,
                'reset_carry_answered_on' => Carbon::today(),
            ]);

            $fields = CustomBudgetField::where('custom_budget_id', $budget->id)->get();

            foreach ($fields as $field) {
                $percent = $oldBudget > 0 ? $field->field_amount / $oldBudget : 0;
                $field->update(['field_amount' => $newBudget * $percent]);
            }

            $snapshot = CustomBudgetSnapshot::where('user_id', $budget->user_id)
                ->where('month', Carbon::now()->format('n'))
                ->orderByDesc('id')
                ->first();

            if ($snapshot) {
                $snapshot->update(['budget_amount' => $newBudget]);

                $snapshotFields = CustomBudgetFieldSnapshot::where('custom_budget_snapshot_id', $snapshot->id)->get();

                foreach ($snapshotFields as $field) {
                    $percent = $oldBudget > 0 ? $field->field_amount / $oldBudget : 0;
                    $field->update(['field_amount' => $newBudget * $percent]);
                }
            }
        } else {
            $budget->update(['reset_carry_answered_on' => Carbon::today()]);
        }
    }

    private function shouldPrompt(AutoBudget|CustomBudget $budget): bool
    {
        if ((int) $budget->reset_date !== (int) Carbon::now()->day) {
            return false;
        }

        return $budget->reset_carry_answered_on?->isToday() !== true;
    }

    private function customNonResetableFields(CustomBudget $budget)
    {
        $fieldIds = CustomBudgetField::where('custom_budget_id', $budget->id)->pluck('id');

        $nonResetableIds = CustomBudgetFieldNonResetable::whereIn('custom_budget_field_id', $fieldIds)
            ->pluck('custom_budget_field_id');

        return CustomBudgetField::whereIn('id', $nonResetableIds)->get();
    }
}
