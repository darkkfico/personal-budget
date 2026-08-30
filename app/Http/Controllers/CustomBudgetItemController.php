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

        CustomBudgetItem::create([
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

        CustomBudgetItemSnapshot::find($item->id)?->update([
            'item_amount' => $item->item_amount,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        return redirect()->route('custom.index');
    }

    public function delete(CustomBudgetItem $item)
    {
        CustomBudgetItem::query()->whereKey($item->id)->delete();

        return redirect()->route('custom.index');
    }
}
