<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BudgetHistoryService
{
    public function summarizeAuto(int $userId): array
    {
        return $this->summarize($userId, [
            'snapshots' => 'auto_budget_snapshots',
            'fields' => 'auto_budget_field_snapshots',
            'items' => 'auto_budget_item_snapshots',
            'fieldFk' => 'auto_budget_snapshot_id',
            'itemFk' => 'auto_budget_field_snapshot_id',
        ]);
    }

    public function summarizeCustom(int $userId): array
    {
        return $this->summarize($userId, [
            'snapshots' => 'custom_budget_snapshots',
            'fields' => 'custom_budget_field_snapshots',
            'items' => 'custom_budget_item_snapshots',
            'fieldFk' => 'custom_budget_snapshot_id',
            'itemFk' => 'custom_budget_field_snapshot_id',
        ]);
    }

    private function summarize(int $userId, array $tables): array
    {
        $snapshots = DB::table($tables['snapshots'])
            ->where('user_id', $userId)
            ->orderByDesc('snapshot')
            ->orderByDesc('id')
            ->get(['id', 'budget_amount', 'currency', 'snapshot']);

        if ($snapshots->isEmpty()) {
            return ['months' => [], 'chartMonths' => []];
        }

        $fields = DB::table($tables['fields'].' as f')
            ->join($tables['snapshots'].' as s', 's.id', '=', 'f.'.$tables['fieldFk'])
            ->where('s.user_id', $userId)
            ->orderByDesc('s.snapshot')
            ->get(['s.id as snapshot_id', 'f.field_name', 'f.field_amount']);

        $itemRows = DB::table($tables['items'].' as i')
            ->join($tables['fields'].' as f', 'f.id', '=', 'i.'.$tables['itemFk'])
            ->join($tables['snapshots'].' as s', 's.id', '=', 'f.'.$tables['fieldFk'])
            ->where('s.user_id', $userId)
            ->orderBy('i.id')
            ->get([
                'i.item_name',
                'i.item_amount',
                'i.field_name',
                'i.snapshot as item_at',
            ]);

        $fieldsBySnapshot = [];
        foreach ($fields as $field) {
            $fieldsBySnapshot[$field->snapshot_id][] = $field;
        }

        $monthKeys = $snapshots
            ->map(fn ($snap) => Carbon::parse($snap->snapshot)->format('Y-m'))
            ->merge($itemRows->map(fn ($item) => Carbon::parse($item->item_at)->format('Y-m')))
            ->unique()
            ->sortDesc()
            ->values();

        $months = [];
        $chartMonths = [];

        foreach ($monthKeys as $monthKey) {
            $budgetSnap = $snapshots->first(
                fn ($snap) => Carbon::parse($snap->snapshot)->format('Y-m') <= $monthKey
            ) ?? $snapshots->last();

            $sections = [];

            foreach ($fieldsBySnapshot[$budgetSnap->id] ?? [] as $field) {
                $sections[$field->field_name] = [
                    'name' => $field->field_name,
                    'allocated' => $field->field_amount,
                    'spent' => 0,
                    'items' => [],
                ];
            }

            foreach ($itemRows as $item) {
                if (Carbon::parse($item->item_at)->format('Y-m') !== $monthKey) {
                    continue;
                }

                $name = $item->field_name;

                if (! isset($sections[$name])) {
                    $sections[$name] = [
                        'name' => $name,
                        'allocated' => 0,
                        'spent' => 0,
                        'items' => [],
                    ];
                }

                $sections[$name]['items'][] = [
                    'name' => $item->item_name,
                    'amount' => $item->item_amount,
                ];
                $sections[$name]['spent'] += (float) $item->item_amount;
            }

            $sectionList = array_values($sections);
            $spentTotal = array_sum(array_column($sectionList, 'spent'));
            $label = Carbon::createFromFormat('Y-m', $monthKey)->format('F Y');
            $budget = $budgetSnap->budget_amount;
            $currency = $budgetSnap->currency;
            $left = $budget - $spentTotal;

            $months[] = [
                'key' => $monthKey,
                'label' => $label,
                'currency' => $currency,
                'budget' => $budget,
                'spent' => $spentTotal,
                'left' => $left,
                'sections' => $sectionList,
            ];

            $chartMonths[$label] = [
                'currency' => $currency,
                'budget' => $budget,
                'spent' => $spentTotal,
                'left' => $left,
                'sections' => array_map(static fn ($section) => [
                    'name' => $section['name'],
                    'spent' => $section['spent'],
                    'allocated' => $section['allocated'],
                ], $sectionList),
            ];
        }

        return [
            'months' => $months,
            'chartMonths' => $chartMonths,
        ];
    }
}
