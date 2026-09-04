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
    public function autoPrompt(AutoBudget $budget): ?array
    {
        if (! $this->shouldPrompt($budget)) {
            return null;
        }

        $amount = $this->captureLeftover($budget);

        if ($amount <= 0) {
            $this->markAnswered($budget);

            return null;
        }

        return $this->promptPayload($budget, $amount, $budget->autoBudgetFields()->orderBy('id')->get());
    }

    public function customPrompt(CustomBudget $budget): ?array
    {
        if (! $this->shouldPrompt($budget)) {
            return null;
        }

        $amount = $this->captureLeftover($budget);

        if ($amount <= 0) {
            $this->markAnswered($budget);

            return null;
        }

        return $this->promptPayload($budget, $amount, $budget->customBudgetFields()->orderBy('id')->get());
    }

    public function captureLeftover(AutoBudget|CustomBudget $budget): float
    {
        if ($budget->reset_leftover_captured_on?->isToday() && $budget->pending_reset_leftover !== null) {
            return (float) $budget->pending_reset_leftover;
        }

        $budget->loadMissing($budget instanceof AutoBudget ? 'autoBudgetFields' : 'customBudgetFields');

        $amount = $this->computeFromItems($budget);

        $budget->update([
            'pending_reset_leftover' => $amount,
            'reset_leftover_captured_on' => Carbon::today(),
        ]);

        return $amount;
    }

    public function applyAuto(AutoBudget $budget, int $fieldId): void
    {
        $field = AutoBudgetField::where('auto_budget_id', $budget->id)->where('id', $fieldId)->first();

        if (! $field || ! $this->shouldPrompt($budget)) {
            return;
        }

        $amount = $this->captureLeftover($budget);

        if ($amount <= 0) {
            $this->markAnswered($budget);

            return;
        }

        $newBudget = $budget->budget_amount + $amount;

        $budget->update([
            'budget_amount' => $newBudget,
            'reset_carry_answered_on' => Carbon::today(),
            'pending_reset_leftover' => null,
        ]);

        $field->update(['field_amount' => $field->field_amount + $amount]);

        $snapshot = AutoBudgetSnapshot::where('user_id', $budget->user_id)
            ->where('month', Carbon::now()->format('n'))
            ->orderByDesc('id')
            ->first();

        if ($snapshot) {
            $snapshot->update(['budget_amount' => $newBudget]);

            AutoBudgetFieldSnapshot::where('auto_budget_snapshot_id', $snapshot->id)
                ->where('field_name', $field->field_name)
                ->update(['field_amount' => $field->field_amount]);
        }
    }

    public function applyCustom(CustomBudget $budget, int $fieldId): void
    {
        $field = CustomBudgetField::where('custom_budget_id', $budget->id)->where('id', $fieldId)->first();

        if (! $field || ! $this->shouldPrompt($budget)) {
            return;
        }

        $amount = $this->captureLeftover($budget);

        if ($amount <= 0) {
            $this->markAnswered($budget);

            return;
        }

        $newBudget = $budget->budget_amount + $amount;

        $budget->update([
            'budget_amount' => $newBudget,
            'reset_carry_answered_on' => Carbon::today(),
            'pending_reset_leftover' => null,
        ]);

        $field->update(['field_amount' => $field->field_amount + $amount]);

        $snapshot = CustomBudgetSnapshot::where('user_id', $budget->user_id)
            ->where('month', Carbon::now()->format('n'))
            ->orderByDesc('id')
            ->first();

        if ($snapshot) {
            $snapshot->update(['budget_amount' => $newBudget]);

            CustomBudgetFieldSnapshot::where('custom_budget_snapshot_id', $snapshot->id)
                ->where('field_name', $field->field_name)
                ->update(['field_amount' => $field->field_amount]);
        }
    }

    private function shouldPrompt(AutoBudget|CustomBudget $budget): bool
    {
        if (! BudgetResetDate::isResetDay((int) $budget->reset_date)) {
            return false;
        }

        return $budget->reset_carry_answered_on?->isToday() !== true;
    }

    private function markAnswered(AutoBudget|CustomBudget $budget): void
    {
        $budget->update([
            'reset_carry_answered_on' => Carbon::today(),
            'pending_reset_leftover' => null,
        ]);
    }

    private function computeFromItems(AutoBudget|CustomBudget $budget): float
    {
        if ($budget instanceof AutoBudget) {
            $leftover = $budget->autoBudgetFields
                ->where('field_name', '!=', 'Savings')
                ->sum(function (AutoBudgetField $field) {
                    $spent = $field->autoBudgetItems()->sum('item_amount');

                    return max(0, (float) $field->field_amount - (float) $spent);
                });
        } else {
            $skipIds = $this->customNonResetableFieldIds($budget);
            $leftover = $budget->customBudgetFields
                ->whereNotIn('id', $skipIds->all())
                ->sum(function (CustomBudgetField $field) {
                    $spent = $field->customBudgetItems()->sum('item_amount');

                    return max(0, (float) $field->field_amount - (float) $spent);
                });
        }

        return $this->roundAmount((float) $leftover, $budget->currency);
    }

    private function promptPayload(AutoBudget|CustomBudget $budget, float $amount, $fields): array
    {
        return [
            'amount' => $amount,
            'currency' => $budget->currency,
            'budget_amount' => $budget->budget_amount,
            'new_budget' => $this->roundAmount($budget->budget_amount + $amount, $budget->currency),
            'sections' => $fields->map(fn ($field) => [
                'id' => $field->id,
                'name' => $field->field_name,
            ])->all(),
        ];
    }

    private function customNonResetableFieldIds(CustomBudget $budget)
    {
        $fieldIds = CustomBudgetField::where('custom_budget_id', $budget->id)->pluck('id');

        return CustomBudgetFieldNonResetable::whereIn('custom_budget_field_id', $fieldIds)
            ->pluck('custom_budget_field_id');
    }

    private function roundAmount(float $amount, string $currency): float
    {
        if ($currency === 'MKD') {
            return (float) round($amount);
        }

        return round($amount, 2);
    }
}
