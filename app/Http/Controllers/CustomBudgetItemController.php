<?php

namespace App\Http\Controllers;

use App\Models\CustomBudgetField;
use App\Models\CustomBudgetFieldSnapshot;
use App\Models\CustomBudgetItem;
use App\Models\CustomBudgetItemSnapshot;
use Illuminate\Http\Request;

class CustomBudgetItemController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'item' => ['required'],
            'field' => ['required'],
        ]);

        $field = CustomBudgetField::where('custom_budget_id', session('budget_id'))
            ->where('field_name', $request->input('field'))
            ->firstOrFail();

        $liveItem = CustomBudgetItem::create([
            'item_name' => $request->item,
            'custom_budget_field_id' => $field->id,
            'item_amount' => $request->amount ?? 0,
            'field_name' => $field->field_name,
        ]);

        $fieldSnapshot = CustomBudgetFieldSnapshot::where('field_name', $field->field_name)
            ->latest('id')
            ->firstOrFail();

        CustomBudgetItemSnapshot::create([
            'item_name' => $request->item,
            'custom_budget_field_snapshot_id' => $fieldSnapshot->id,
            'custom_budget_item_id' => $liveItem->id,
            'item_amount' => $request->amount ?? 0,
            'field_name' => $field->field_name,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        return redirect()->route('custom.index');
    }

    public function edit(Request $request, CustomBudgetItem $item)
    {
        if ($request->filled('adjust')) {
            $request->validate([
                'adjust' => ['numeric', 'not_in:0'],
            ]);
            $newAmount = max(0, (int) $item->item_amount + (int) $request->input('adjust'));
        } else {
            $request->validate([
                'item_amount' => ['required', 'numeric', 'min:0'],
            ]);
            $newAmount = $request->item_amount;
        }

        $item->update(['item_amount' => $newAmount]);

        $item->refresh();

        CustomBudgetItemSnapshot::where('custom_budget_item_id', $item->id)->update([
            'item_amount' => $item->item_amount,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        return redirect()->route('custom.index');
    }

    public function delete(Request $request, CustomBudgetItem $item)
    {
        $permanent = $request->boolean('permanent');

        if ($permanent) {
            CustomBudgetItemSnapshot::where('custom_budget_item_id', $item->id)->delete();

            $fallback = CustomBudgetItemSnapshot::whereNull('custom_budget_item_id')
                ->where('field_name', $item->field_name)
                ->where('item_name', $item->item_name)
                ->orderByDesc('id')
                ->limit(1)
                ->pluck('id');

            CustomBudgetItemSnapshot::whereIn('id', $fallback)->delete();
        }

        $item->delete();

        return redirect()->route('custom.index');
    }
}
