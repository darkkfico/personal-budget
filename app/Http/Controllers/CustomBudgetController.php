<?php

namespace App\Http\Controllers;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\CustomBudget;
use App\Models\CustomBudgetFieldNonResetable;
use App\Models\CustomBudgetFieldSnapshot;
use App\Models\CustomBudgetItem;
use App\Models\CustomBudgetItemSnapshot;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetSnapshot;
use App\Services\BudgetHistoryService;
use App\Services\DailyAllowenceService;
use App\Services\OnboardingService;
use App\Services\ResetCarryoverService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomBudgetController extends Controller
{
    public function form(OnboardingService $onboarding)
    {
        $user = Auth::user();

        if (AutoBudget::where("user_id", $user->id)->exists() || CustomBudget::where('user_id', $user->id)->exists()) {
            return $onboarding->redirect($user);
        }

        $onboarding->mark($user, OnboardingService::CUSTOM_FORM);

        $currencies = Storage::json('currencies.json');

        return view("custom.form", compact("currencies"));
    }

    public function store(Request $request, OnboardingService $onboarding)
    {

        if(AutoBudget::where("user_id", Auth::id())->exists() || CustomBudget::where('user_id', Auth::id())->exists()){
            return $onboarding->redirect(Auth::user());
        }

        $rules = [
            "budget" => ["required"],
            "currency" => ["required", "in:MKD,EUR,USD"],
            "reset_date" => ["required", "integer", "min:1", "max:31"]
        ];

        $sectionNumber = 1;

        $sum = 0;

        while ($request->has("custom-field" . $sectionNumber)) {
            $rules["custom-field{$sectionNumber}"] = ["required", "string"];
            $rules["custom-field{$sectionNumber}-amount"] = ["required", "numeric"];

            $sum += $request->input("custom-field" . $sectionNumber . "-amount");
            $sectionNumber++;
        }

        $request->validate($rules);

        if ($sum != 100) {
            return back()->withErrors([
                "sum" => "Sum of the percentage must be 100!",
            ]);
        }

        $customBudget = CustomBudget::create([
            'user_id' => Auth::id(),
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date
        ]);


        $customBudgetSnapshot = CustomBudgetSnapshot::create([
            'user_id' => Auth::id(),
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
            "snapshot" => now(),
            "month" => Carbon::now()->format('n'),
        ]);

        $i = 1;

        while ($request->has("custom-field" . $i)) {
            CustomBudgetField::create([
                "custom_budget_id" => $customBudget->id,
                "field_name" => $request->input("custom-field{$i}"),
                "field_amount" => $customBudget->budget_amount * ($request->input("custom-field{$i}-amount") / 100),
            ]);
            CustomBudgetFieldSnapshot::create([
                "custom_budget_snapshot_id" => $customBudgetSnapshot->id,
                "field_name" => $request->input("custom-field{$i}"),
                "field_amount" => $customBudgetSnapshot->budget_amount * ($request->input("custom-field{$i}-amount") / 100),
                "snapshot" => now(),
                "month" => Carbon::now()->format('n'),
            ]);
            $i++;
        }

        $onboarding->mark(Auth::user(), OnboardingService::CUSTOM_NON_RESETABLE);

        return redirect()->route("custom.formNonResetable");
    }

    public function nonResetable(OnboardingService $onboarding)
    {
        $budget = CustomBudget::with('customBudgetFields')->where('user_id', Auth::id())->first();

        if (! $budget) {
            return $onboarding->redirect(Auth::user());
        }

        $fields = $budget->customBudgetFields()->orderBy('id')->get();
        $selectedNonResetableId = CustomBudgetFieldNonResetable::whereIn('custom_budget_field_id', $fields->pluck('id'))
            ->value('custom_budget_field_id');

        return view("custom.nonResetField", compact("fields", "selectedNonResetableId"));
    }

    public function storeNonResetable(Request $request, OnboardingService $onboarding)
    {
        $onboarding->mark(Auth::user(), OnboardingService::COMPLETE);

        $budget = CustomBudget::where('user_id', Auth::id())->firstOrFail();
        $fieldIds = CustomBudgetField::where('custom_budget_id', $budget->id)->pluck('id');

        CustomBudgetFieldNonResetable::whereIn('custom_budget_field_id', $fieldIds)->delete();

        if ($request->field !== 'none') {
            CustomBudgetFieldNonResetable::create([
                'custom_budget_field_id' => $request->field,
            ]);
        }

        return redirect()->route("custom.index");
    }

    public function index(ResetCarryoverService $carryover, DailyAllowenceService $allowence, OnboardingService $onboarding)
    {
        $budget = CustomBudget::where("user_id", Auth::id())->first();

        if (! $budget) {
            return $onboarding->redirect(Auth::user());
        }
        $items = $budget->customBudgetItems;
        $fields = CustomBudgetField::where('custom_budget_id', $budget->id)->get();

        session(['budget_id' => $budget->id, 'type' => 'custom']);

        $nonResetableFieldIds = CustomBudgetFieldNonResetable::whereIn('custom_budget_field_id', $fields->pluck('id'))
            ->pluck('custom_budget_field_id');

        ['sub1' => $sub1, 'daysDiff' => $daysDiff, 'warnDaily' => $warnDaily] = $allowence->forCustom($budget, $items, $nonResetableFieldIds);

        $resetCarryover = $carryover->customPrompt($budget);

        return view("custom.index", compact("items", "budget", "fields", "daysDiff", "sub1", "resetCarryover", "warnDaily"));
    }

    public function applyResetCarryover(ResetCarryoverService $carryover, DailyAllowenceService $allowence)
    {
        $budget = CustomBudget::where("user_id", Auth::id())->firstOrFail();

        $carryover->applyCustom($budget, request()->boolean("apply"));

        $allowence->clear();

        return redirect()->route("custom.index");
    }

    public function change()
    {
        $currencies = Storage::json('currencies.json');
        $budget = CustomBudget::where("user_id", Auth::id())->first();
        $id = $budget->id;
        $budgetAmount = $budget->budget_amount;
        $fields = CustomBudgetField::where('custom_budget_id', $id)->orderBy('id')->get();

        return view("custom.change", compact("id", "currencies", "fields", "budgetAmount", "budget"));
    }

    public function edit(Request $request, DailyAllowenceService $allowence)
    {

        $allowence->clear();

        $rules = [
            "budget" => ["required"],
            "currency" => ["required", "in:MKD,EUR,USD"],
            "reset_date" => ["required", "integer", "min:1", "max:31"]
        ];

        $sectionNumber = 1;

        $sum = 0;


        while ($request->has("custom-field" . $sectionNumber)) {
            $rules["custom-field" . $sectionNumber] = ["required", "string"];
            $rules["custom-field" . $sectionNumber . "-amount"] = ["required", "numeric"];

            $sum += $request->input("custom-field" . $sectionNumber . "-amount");
            $sectionNumber++;
        }

        $request->validate($rules);

        if ($sum != 100) {
            return back()->withErrors([
                "sum" => "Sum of the percentage must be 100!",
            ]);
        }

        $submitted = [];
        $sectionNumber = 1;
        while ($request->has("custom-field" . $sectionNumber)) {
            $submitted[] = [
                'name' => $request->input("custom-field{$sectionNumber}"),
                'percent' => $request->input("custom-field{$sectionNumber}-amount"),
            ];
            $sectionNumber++;
        }

        $customBudget = CustomBudget::find($request->budget_id);
        $fields = CustomBudgetField::where('custom_budget_id', $customBudget->id)->orderBy('id')->get();
        $customBudgetSnapshot = CustomBudgetSnapshot::where('user_id', Auth::id())->orderByDesc('id')->first();
        $fieldsSnapshot = $customBudgetSnapshot
            ? CustomBudgetFieldSnapshot::where("custom_budget_snapshot_id", $customBudgetSnapshot->id)->orderBy("id")->get()
            : collect();

        $customBudget->update([
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
            'reset_carry_answered_on' => (int) $customBudget->reset_date === (int) $request->reset_date
                ? $customBudget->reset_carry_answered_on
                : null,
        ]);

        if ($customBudgetSnapshot) {
            $customBudgetSnapshot->update([
                'budget_amount' => $request->budget,
                'currency' => $request->currency,
                'reset_date' => $request->reset_date
            ]);
        }

        foreach ($fields as $index => $field) {
            if (!isset($submitted[$index])) {
                $field->delete();
                continue;
            }

            $newName = $submitted[$index]['name'];

            CustomBudgetItem::where('custom_budget_field_id', $field->id)->update([
                'field_name' => $newName,
            ]);

            $field->update([
                'field_name' => $newName,
                'field_amount' => $customBudget->budget_amount * ($submitted[$index]['percent'] / 100),
            ]);
        }

        for ($index = $fields->count(); $index < count($submitted); $index++) {
            CustomBudgetField::create([
                'custom_budget_id' => $customBudget->id,
                'field_name' => $submitted[$index]['name'],
                'field_amount' => $customBudget->budget_amount * ($submitted[$index]['percent'] / 100),
            ]);
        }

        if ($customBudgetSnapshot) {
            foreach ($fieldsSnapshot as $index => $field) {
                if (!isset($submitted[$index])) {
                    $field->delete();
                    continue;
                }

                $newName = $submitted[$index]['name'];

                CustomBudgetItemSnapshot::where('custom_budget_field_snapshot_id', $field->id)->update([
                    'field_name' => $newName,
                ]);

                $field->update([
                    'field_name' => $newName,
                    'field_amount' => $customBudget->budget_amount * ($submitted[$index]['percent'] / 100),
                ]);
            }

            for ($index = $fieldsSnapshot->count(); $index < count($submitted); $index++) {
                CustomBudgetFieldSnapshot::create([
                    'custom_budget_snapshot_id' => $customBudgetSnapshot->id,
                    'field_name' => $submitted[$index]['name'],
                    'field_amount' => $customBudget->budget_amount * ($submitted[$index]['percent'] / 100),
                    'snapshot' => now(),
                    'month' => Carbon::now()->format('n'),
                ]);
            }
        }

        session(['type' => 'custom']);

        return redirect()->route("custom.formNonResetable");
    }

    public function convertFromAuto(Request $request, DailyAllowenceService $allowence)
    {
        $autoBudget = AutoBudget::where("user_id", Auth::id())->firstOrFail();

        if (CustomBudget::where("user_id", Auth::id())->exists()) {
            return back()->withErrors([
                "budget" => "A custom budget already exists.",
            ]);
        }

        $rules = [
            "budget" => ["required"],
            "currency" => ["required", "in:MKD,EUR,USD"],
            "reset_date" => ["required", "integer", "min:1", "max:31"],
        ];

        $sectionNumber = 1;
        $sum = 0;

        while ($request->has("custom-field" . $sectionNumber)) {
            $rules["custom-field" . $sectionNumber] = ["required", "string"];
            $rules["custom-field" . $sectionNumber . "-amount"] = ["required", "numeric"];
            $sum += $request->input("custom-field" . $sectionNumber . "-amount");
            $sectionNumber++;
        }

        $request->validate($rules);

        if ($sum != 100) {
            return back()->withErrors([
                "sum" => "Sum of the percentage must be 100!",
            ]);
        }

        $submitted = [];
        $sectionNumber = 1;
        while ($request->has("custom-field" . $sectionNumber)) {
            $submitted[] = [
                "name" => $request->input("custom-field{$sectionNumber}"),
                "percent" => $request->input("custom-field{$sectionNumber}-amount"),
            ];
            $sectionNumber++;
        }

        $allowence->clear();

        $customBudget = CustomBudget::create([
            "user_id" => Auth::id(),
            "budget_amount" => $request->budget,
            "currency" => $request->currency,
            "reset_date" => $request->reset_date,
        ]);

        $customBudgetSnapshot = CustomBudgetSnapshot::create([
            "user_id" => Auth::id(),
            "budget_amount" => $request->budget,
            "currency" => $request->currency,
            "reset_date" => $request->reset_date,
            "snapshot" => now(),
            "month" => Carbon::now()->format("n"),
        ]);

        $autoFields = AutoBudgetField::where("auto_budget_id", $autoBudget->id)
            ->with("autoBudgetItems")
            ->orderBy("id")
            ->get();

        foreach ($submitted as $index => $section) {
            $customField = CustomBudgetField::create([
                "custom_budget_id" => $customBudget->id,
                "field_name" => $section["name"],
                "field_amount" => $customBudget->budget_amount * ($section["percent"] / 100),
            ]);

            $fieldSnapshot = CustomBudgetFieldSnapshot::create([
                "custom_budget_snapshot_id" => $customBudgetSnapshot->id,
                "field_name" => $section["name"],
                "field_amount" => $customBudget->budget_amount * ($section["percent"] / 100),
                "snapshot" => now(),
                "month" => Carbon::now()->format("n"),
            ]);

            $autoField = $autoFields[$index] ?? null;

            if (! $autoField || strcasecmp(trim($autoField->field_name), trim($section["name"])) !== 0) {
                continue;
            }

            foreach ($autoField->autoBudgetItems as $item) {
                $liveItem = CustomBudgetItem::create([
                    "custom_budget_field_id" => $customField->id,
                    "item_name" => $item->item_name,
                    "item_amount" => $item->item_amount,
                    "field_name" => $section["name"],
                ]);

                CustomBudgetItemSnapshot::create([
                    "custom_budget_field_snapshot_id" => $fieldSnapshot->id,
                    "custom_budget_item_id" => $liveItem->id,
                    "field_name" => $section["name"],
                    "item_name" => $item->item_name,
                    "item_amount" => $item->item_amount,
                    "snapshot" => now(),
                    "month" => Carbon::now()->format("n"),
                ]);
            }
        }

        $autoBudget->delete();

        session(["type" => "custom", "budget_id" => $customBudget->id]);

        return redirect()->route("custom.index");
    }

    public function history(CustomBudget $budget, BudgetHistoryService $history)
    {
        abort_unless($budget->user_id === Auth::id(), 403);

        ['months' => $months, 'chartMonths' => $chartMonths] = $history->summarizeCustom($budget->user_id);

        return view('custom.history', compact('budget', 'months', 'chartMonths'));
    }
}
