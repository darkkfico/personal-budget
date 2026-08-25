<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutoBudgetCreateRequest;
use App\Http\Requests\AutoBudgetEditRequest;
use App\Models\AutoBudget;
use App\Models\AutoBudgetFieldSnapshot;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetSnapshot;
use App\Models\CustomBudget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AutoBudgetController extends Controller
{
    private const AUTO_FIELDS = [
        'Groceries' => 0.50,
        'Wishes' => 0.30,
        'Savings' => 0.20,
    ];

    public function form()
    {
        $currencies = Storage::json('currencies.json');

        return view('auto.form', compact('currencies'));
    }

    public function create(AutoBudgetCreateRequest $request)
    {
        if (AutoBudget::where('user_id', Auth::id())->exists() || CustomBudget::where('user_id', Auth::id())->exists()) {
            return back()->withErrors(['exists' => 'This user exists!']);
        }

        $autoBudget = AutoBudget::create([
            'user_id' => Auth::id(),
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
        ]);

        $autoBudgetSnapshot = AutoBudgetSnapshot::create([
            'user_id' => Auth::id(),
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
            'snapshot' => now(),
            'month' => Carbon::now()->format('n'),
        ]);

        foreach (self::AUTO_FIELDS as $fieldName => $percentage) {
            AutoBudgetField::create([
                'auto_budget_id' => $autoBudget->id,
                'field_name' => $fieldName,
                'field_amount' => $request->budget * $percentage,
            ]);

            AutoBudgetFieldSnapshot::create([
                'auto_budget_snapshot_id' => $autoBudgetSnapshot->id,
                'field_name' => $fieldName,
                'field_amount' => $request->budget * $percentage,
                'snapshot' => now(),
                'month' => Carbon::now()->format('n'),
            ]);
        }

        return redirect()->route('auto.index');
    }

    public function index()
    {
        $budget = AutoBudget::where('user_id', Auth::id())
            ->with(['autoBudgetItems', 'autoBudgetFields'])
            ->first();

        $items = $budget->autoBudgetItems;
        $fields = $budget->autoBudgetFields;

        session(['budget_id' => $budget->id]);

        $givenDate = Carbon::createFromDate(now()->year, now()->month, $budget->reset_date);

        if ($givenDate->isPast()) {
            $givenDate->addMonth();
        }

        $daysDiff = Carbon::now()->diffInDays($givenDate);

        $items_amount = $items->sum('item_amount');

        if ($budget->currency == 'MKD') {
            $sub1 = round((($budget->budget_amount * 0.8) - $items_amount) / $daysDiff);
        } else {
            $sub1 = round((($budget->budget_amount * 0.8) - $items_amount) / $daysDiff, 2);
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

        return view('auto.index', compact('budget', 'items', 'fields', 'daysDiff', 'sub1'));
    }

    public function change($id)
    {
        $currencies = Storage::json('currencies.json');

        return view('auto.change', compact('currencies', 'id'));
    }

    public function edit(AutoBudgetEditRequest $request, AutoBudget $budget)
    {
        session()->forget('amount_left');
        session()->forget('deleted_amount');
        session()->forget('last_day');

        $budget->update([
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
        ]);

        $autoBudgetSnapshot = AutoBudgetSnapshot::create([
            'user_id' => Auth::id(),
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
            'snapshot' => now(),
            'month' => Carbon::now()->format('n'),
        ]);

        foreach (self::AUTO_FIELDS as $fieldName => $percentage) {
            AutoBudgetField::where('auto_budget_id', $budget->id)
                ->where('field_name', $fieldName)
                ->update(['field_amount' => $request->budget * $percentage]);

            AutoBudgetFieldSnapshot::create([
                'auto_budget_snapshot_id' => $autoBudgetSnapshot->id,
                'field_name' => $fieldName,
                'field_amount' => $request->budget * $percentage,
                'snapshot' => now(),
                'month' => Carbon::now()->format('n'),
            ]);
        }

        session(['type' => 'auto']);

        return redirect()->route('auto.index');
    }

    public function convert(AutoBudgetEditRequest $request)
    {
        $customBudget = CustomBudget::where('user_id', Auth::id())->first();

        if (!$customBudget) {
            return back()->withErrors(['budget' => 'No custom budget to convert.']);
        }

        session()->forget('amount_left');
        session()->forget('deleted_amount');
        session()->forget('last_day');

        $customBudget->delete();

        $autoBudget = AutoBudget::create([
            'user_id' => Auth::id(),
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
        ]);

        $autoBudgetSnapshot = AutoBudgetSnapshot::create([
            'user_id' => Auth::id(),
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
            'snapshot' => now(),
            'month' => Carbon::now()->format('n'),
        ]);

        foreach (self::AUTO_FIELDS as $fieldName => $percentage) {
            AutoBudgetField::create([
                'auto_budget_id' => $autoBudget->id,
                'field_name' => $fieldName,
                'field_amount' => $request->budget * $percentage,
            ]);

            AutoBudgetFieldSnapshot::create([
                'auto_budget_snapshot_id' => $autoBudgetSnapshot->id,
                'field_name' => $fieldName,
                'field_amount' => $request->budget * $percentage,
                'snapshot' => now(),
                'month' => Carbon::now()->format('n'),
            ]);
        }

        session(['type' => 'auto']);
        session(['budget_id' => $autoBudget->id]);

        return redirect()->route('auto.index');
    }

    public function history(AutoBudget $budget)
    {
        $budgetSnap = AutoBudgetSnapshot::with(['autoBudgetFieldSnapshots.autoBudgetItemSnapshots'])
            ->where('user_id', $budget->user_id)
            ->orderBy('snapshot', 'desc')
            ->get()
            ->groupBy(fn ($snap) => Carbon::createFromDate(null, $snap->month, 1)->format('F Y'));

        return view('auto.history', compact('budgetSnap', 'budget'));
    }
}
