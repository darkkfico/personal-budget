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

    public function itemsAuto(int $userId, string $monthKey, string $fieldName): array
    {
        return $this->items($userId, $monthKey, $fieldName, [
            'snapshots' => 'auto_budget_snapshots',
            'fields' => 'auto_budget_field_snapshots',
            'items' => 'auto_budget_item_snapshots',
            'fieldFk' => 'auto_budget_snapshot_id',
            'itemFk' => 'auto_budget_field_snapshot_id',
        ]);
    }

    public function itemsCustom(int $userId, string $monthKey, string $fieldName): array
    {
        return $this->items($userId, $monthKey, $fieldName, [
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
            ->get(['id', 'budget_amount', 'currency', 'snapshot']);

        if ($snapshots->isEmpty()) {
            return ['months' => [], 'chartMonths' => []];
        }

        $fields = DB::table($tables['fields'].' as f')
            ->join($tables['snapshots'].' as s', 's.id', '=', 'f.'.$tables['fieldFk'])
            ->where('s.user_id', $userId)
            ->orderByDesc('s.snapshot')
            ->get(['s.id as snapshot_id', 's.snapshot', 'f.field_name', 'f.field_amount']);

        $spentRows = DB::table($tables['items'].' as i')
            ->join($tables['fields'].' as f', 'f.id', '=', 'i.'.$tables['itemFk'])
            ->join($tables['snapshots'].' as s', 's.id', '=', 'f.'.$tables['fieldFk'])
            ->where('s.user_id', $userId)
            ->groupBy('s.id', 'i.field_name')
            ->select('s.id as snapshot_id', 'i.field_name', DB::raw('SUM(i.item_amount) as spent'))
            ->get();

        $spentBySnapshotField = [];
        foreach ($spentRows as $row) {
            $spentBySnapshotField[$row->snapshot_id][$row->field_name] = (float) $row->spent;
        }

        $fieldsBySnapshot = [];
        foreach ($fields as $field) {
            $fieldsBySnapshot[$field->snapshot_id][] = $field;
        }

        $months = [];
        $chartMonths = [];

        foreach ($snapshots->groupBy(fn ($snap) => Carbon::parse($snap->snapshot)->format('Y-m')) as $monthKey => $monthSnaps) {
            $latest = $monthSnaps->first();
            $sections = [];
            $spentTotal = 0;

            foreach ($monthSnaps as $snap) {
                foreach ($fieldsBySnapshot[$snap->id] ?? [] as $field) {
                    $name = $field->field_name;

                    if (! isset($sections[$name])) {
                        $sections[$name] = [
                            'name' => $name,
                            'allocated' => $field->field_amount,
                            'spent' => 0,
                        ];
                    }

                    $spent = $spentBySnapshotField[$snap->id][$name] ?? 0;
                    $sections[$name]['spent'] += $spent;
                    $spentTotal += $spent;
                }
            }

            $sectionList = array_values($sections);
            $label = Carbon::parse($latest->snapshot)->format('F Y');
            $budget = $latest->budget_amount;
            $currency = $latest->currency;
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
                'sections' => $sectionList,
            ];
        }

        return [
            'months' => $months,
            'chartMonths' => $chartMonths,
        ];
    }

    private function items(int $userId, string $monthKey, string $fieldName, array $tables): array
    {
        [$year, $month] = array_pad(explode('-', $monthKey), 2, null);

        if (! $year || ! $month) {
            return [];
        }

        return DB::table($tables['items'].' as i')
            ->join($tables['fields'].' as f', 'f.id', '=', 'i.'.$tables['itemFk'])
            ->join($tables['snapshots'].' as s', 's.id', '=', 'f.'.$tables['fieldFk'])
            ->where('s.user_id', $userId)
            ->where('i.field_name', $fieldName)
            ->whereYear('s.snapshot', (int) $year)
            ->whereMonth('s.snapshot', (int) $month)
            ->orderBy('i.id')
            ->get(['i.item_name', 'i.item_amount'])
            ->map(fn ($item) => [
                'name' => $item->item_name,
                'amount' => $item->item_amount,
            ])
            ->all();
    }
}
