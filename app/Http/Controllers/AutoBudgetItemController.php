<?php

namespace App\Http\Controllers;

use App\Models\AutoBudgetField;
use App\Models\AutoBudgetFieldSnapshot;
use App\Models\AutoBudgetItem;
use App\Models\AutoBudgetItemSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoBudgetItemController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'item' => ['required'],
            'field' => ['required'],
        ]);

        $field = AutoBudgetField::where('auto_budget_id', session('budget_id'))
            ->where('field_name', $request->input('field'))
            ->firstOrFail();

        $liveItem = AutoBudgetItem::create([
            'item_name' => $request->item,
            'auto_budget_field_id' => $field->id,
            'item_amount' => $request->amount ?? 0,
            'field_name' => $field->field_name,
        ]);

        $fieldSnapshot = AutoBudgetFieldSnapshot::query()
            ->where('field_name', $field->field_name)
            ->whereHas('autoBudgetSnapshot', fn ($query) => $query->where('user_id', Auth::id()))
            ->latest('id')
            ->firstOrFail();

        AutoBudgetItemSnapshot::create([
            'item_name' => $request->item,
            'auto_budget_field_snapshot_id' => $fieldSnapshot->id,
            'auto_budget_item_id' => $liveItem->id,
            'item_amount' => $request->amount ?? 0,
            'field_name' => $field->field_name,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        return redirect()->route('auto.index');
    }

    public function edit(Request $request, AutoBudgetItem $item)
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

        AutoBudgetItemSnapshot::where('auto_budget_item_id', $item->id)->update([
            'item_amount' => $item->item_amount,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        return redirect()->route('auto.index');
    }

    public function delete(Request $request, AutoBudgetItem $item)
    {
        $permanent = $request->boolean('permanent');

        if ($permanent) {
            AutoBudgetItemSnapshot::where('auto_budget_item_id', $item->id)->delete();

            $fallback = AutoBudgetItemSnapshot::whereNull('auto_budget_item_id')
                ->where('field_name', $item->field_name)
                ->where('item_name', $item->item_name)
                ->orderByDesc('id')
                ->limit(1)
                ->pluck('id');

            AutoBudgetItemSnapshot::whereIn('id', $fallback)->delete();
        }

        $item->delete();

        return redirect()->route('auto.index');
    }
}
