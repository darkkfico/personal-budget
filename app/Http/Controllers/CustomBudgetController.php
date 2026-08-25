<?php

namespace App\Http\Controllers;

use App\Models\AutoBudget;
use App\Models\CustomBudget;
use App\Models\CustomBudgetFieldNonResetable;
use App\Models\CustomBudgetFieldSnapshot;
use App\Models\CustomBudgetItem;
use App\Models\CustomBudgetItemSnapshot;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetSnapshot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomBudgetController extends Controller
{
    public function form()
    {
        $currencies = Storage::json('currencies.json');

        return view("custom.form", compact("currencies"));
    }

    public function store(Request $request)
    {

        if(AutoBudget::where("user_id", Auth::id())->exists() || CustomBudget::where('user_id', Auth::id())->exists()){
            return back()->withErrors(["exists" => "This user exists!"]);
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


        return redirect()->route("custom.formNonResetable");
    }

    public function nonResetable()
    {
        $budget = CustomBudget::with('customBudgetFields')->where('user_id', Auth::id())->first();

        $fields = $budget->customBudgetFields;

        return view("custom.nonResetField", compact("fields"));
    }

    public function storeNonResetable(Request $request)
    {
        if($request->field == "none"){
            return redirect()->route("custom.index");
        } else{
            CustomBudgetFieldNonResetable::create([
                'custom_budget_field_id' => $request->field
            ]);

            return redirect()->route("custom.index");
        }
    }

    public function index()
    {
        $budget = CustomBudget::where("user_id", Auth::id())->first();
        $items = $budget->customBudgetItems;
        $fields = CustomBudgetField::where('custom_budget_id', $budget->id)->get();

        session(['budget_id' => $budget->id]);

        $givenDate = Carbon::createFromDate(now()->year, now()->month, $budget->reset_date);

        if ($givenDate->isPast()) {
            $givenDate->addMonth();
        }

        $daysDiff = Carbon::now()->diffInDays($givenDate);

        $items_amount = $items->sum("item_amount");

        if($budget->currency == "MKD"){
            $sub1 = round(($budget->budget_amount - $items_amount) / $daysDiff);
        } else{
            $sub1 = round(($budget->budget_amount - $items_amount) / $daysDiff, 2);
        }

        if (session('amount_left')) {
            if (session('deleted_amount')) {
                session(['amount_left' => (session('amount_left') + session('deleted_amount'))]);
                session()->forget('deleted_amount');
                $sub1 = session('amount_left') - $items_amount;
            } elseif (session('last_day')) {
                if (session('last_day') != Carbon::now()->day) {
                    session(['last_day' => Carbon::now()->day]);
                    $todaysAmount = $items_amount + session('amount_left');
                    session(['amount_left' => $todaysAmount]);
                    $sub1 = session('amount_left') - $items_amount;
                } else {
                    $sub1 = session('amount_left') - $items_amount;
                }
            }
        } else {
            session(['amount_left' => $sub1]);
            session(['last_day' => Carbon::now()->day]);
        }

        return view("custom.index", compact("items", "budget", "fields", "daysDiff", "sub1"));
    }

    public function change()
    {

        $currencies = Storage::json('currencies.json');
        $budget = CustomBudget::where("user_id", Auth::id())->first();
        $id = $budget->id;
        $fields = CustomBudgetField::where('custom_budget_id', $id)->get();

        return view("custom.change", compact("id", "currencies", "fields"));
    }

    public function edit(Request $request)
    {
        session()->forget("amount_left");
        session()->forget("deleted_amount");
        session()->forget("last_day");

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

        $autoBudget = AutoBudget::where('user_id', Auth::id())->first();
        $customBudget = CustomBudget::where('user_id', Auth::id())->first();

        if ($autoBudget && !$customBudget) {
            return $this->convertFromAuto($request, $autoBudget);
        }

        $customBudget = CustomBudget::find($request->budget_id);
        $fields = CustomBudgetField::where('custom_budget_id', $customBudget->id)->orderBy('id')->get();
        $customBudgetSnapshot = CustomBudgetSnapshot::find($request->budget_id);
        $fieldsSnapshot = CustomBudgetFieldSnapshot::where("custom_budget_snapshot_id", $customBudget->id)->orderBy("id")->get();

        $i = 1;

        foreach ($fields as $field) {
            while ($request->has(("custom-field{$i}"))) {
                if (CustomBudgetItem::where("field_name", $field->field_name)) {
                    CustomBudgetItem::where("field_name", $field->field_name)->update([
                        'field_name' => $request->input("custom-field{$i}")
                    ]);
                }
                $i++;
            }
        }

        foreach ($fieldsSnapshot as $field) {
            while ($request->has(("custom-field{$i}"))) {
                if (CustomBudgetItemSnapshot::where("field_name", $field->field_name)) {
                    CustomBudgetItemSnapshot::where("field_name", $field->field_name)->update([
                        'field_name' => $request->input("custom-field{$i}")
                    ]);
                }
                $i++;
            }
        }

        $customBudget->update([
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date
        ]);

        $customBudgetSnapshot->update([
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date
        ]);

        $i = 1;

        foreach ($fields as $field) {
            if ($request->input("custom-field{$i}")) {
                $field->update([
                    'field_name' => $request->input("custom-field{$i}"),
                    'field_amount' => $customBudget->budget_amount * ($request->input("custom-field{$i}-amount") / 100)
                ]);
            }
            $i++;
        }

        foreach ($fieldsSnapshot as $field) {
            if ($request->input("custom-field{$i}")) {
                $field->update([
                    'field_name' => $request->input("custom-field{$i}"),
                    'field_amount' => $customBudget->budget_amount * ($request->input("custom-field{$i}-amount") / 100)
                ]);
            }
            $i++;
        }

        session(['type' => 'custom']);

        return redirect()->route("custom.index");
    }

    private function convertFromAuto(Request $request, AutoBudget $autoBudget)
    {
        $autoBudget->delete();

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

        session(['type' => 'custom']);
        session(['budget_id' => $customBudget->id]);

        return redirect()->route("custom.formNonResetable");
    }

    public function history(CustomBudget $budget){

        $budgetSnap = CustomBudgetSnapshot::with(['customBudgetFieldSnapshots.customBudgetItemSnapshots'])->where('user_id', $budget->user_id)->orderBy('snapshot', 'desc')->get()->groupBy(fn ($snap) => Carbon::createFromDate(null, $snap->month, 1)->format('F Y'));
        
        return view("custom.history", compact("budgetSnap", "budget"));
    }
}
