<?php

namespace App\Services;

use Illuminate\Http\Request;

class CustomBudgetAllocation
{
    public const OK = 'ok';

    public const OVER = 'over';

    public const LEFTOVER = 'leftover';

    private const TOLERANCE = 0.05;

    public function resolve(Request $request): string
    {
        $sum = $this->percentSum($request);

        if ($sum > 100 + self::TOLERANCE) {
            return self::OVER;
        }

        $leftoverMoney = $this->leftoverMoney($request, $sum);

        if ($leftoverMoney <= 0) {
            return self::OK;
        }

        $target = (int) $request->input('leftover_section');

        if ($target > 0 && $request->filled("custom-field{$target}")) {
            $request->merge([
                "custom-field{$target}-amount" => (float) $request->input("custom-field{$target}-amount") + (100 - $sum),
            ]);

            return self::OK;
        }

        return self::LEFTOVER;
    }

    public function popup(Request $request): array
    {
        $sum = $this->percentSum($request);
        $sections = [];
        $i = 1;

        while ($request->has("custom-field{$i}")) {
            $name = trim((string) $request->input("custom-field{$i}"));

            if ($name !== '') {
                $sections[$i] = $name;
            }

            $i++;
        }

        return [
            'amount' => $this->leftoverMoney($request, $sum),
            'currency' => $request->input('currency'),
            'sections' => $sections,
        ];
    }

    private function percentSum(Request $request): float
    {
        $sum = 0;
        $i = 1;

        while ($request->has("custom-field{$i}")) {
            $sum += (float) $request->input("custom-field{$i}-amount");
            $i++;
        }

        return $sum;
    }

    private function leftoverMoney(Request $request, float $sum): float
    {
        $leftoverPercent = max(0, 100 - $sum);
        $amount = ((float) $request->input('budget')) * ($leftoverPercent / 100);

        if ($request->input('currency') === 'MKD') {
            return (float) round($amount);
        }

        return round($amount, 2);
    }
}
