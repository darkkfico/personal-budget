<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutoBudgetCreateRequest;
use App\Http\Requests\AutoBudgetEditRequest;
use App\Models\AutoBudget;
use App\Models\AutoBudgetFieldSnapshot;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetSnapshot;
use App\Models\CustomBudget;
use App\Services\BudgetHistoryService;
use App\Services\DailyAllowenceService;
use App\Services\OnboardingService;
use App\Services\ResetCarryoverService;
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

    public function form(OnboardingService $onboarding)
    {
        $user = Auth::user();

        if (AutoBudget::where('user_id', $user->id)->exists() || CustomBudget::where('user_id', $user->id)->exists()) {
            return $onboarding->redirect($user);
        }

        $onboarding->mark($user, OnboardingService::AUTO_FORM);

        $currencies = Storage::json('currencies.json');

        return view('auto.form', compact('currencies'));
    }

    public function create(AutoBudgetCreateRequest $request, OnboardingService $onboarding)
    {
        if (AutoBudget::where('user_id', Auth::id())->exists() || CustomBudget::where('user_id', Auth::id())->exists()) {
            return $onboarding->redirect(Auth::user());
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

        $onboarding->mark(Auth::user(), OnboardingService::COMPLETE);

        return redirect()->route('auto.index');
    }

    public function index(ResetCarryoverService $carryover, DailyAllowenceService $allowence, OnboardingService $onboarding)
    {
        $budget = AutoBudget::where('user_id', Auth::id())
            ->with(['autoBudgetItems', 'autoBudgetFields'])
            ->first();

        if (! $budget) {
            return $onboarding->redirect(Auth::user());
        }

        $items = $budget->autoBudgetItems;
        $fields = $budget->autoBudgetFields;

        session(['budget_id' => $budget->id, 'type' => 'auto']);

        ['sub1' => $sub1, 'daysDiff' => $daysDiff, 'warnDaily' => $warnDaily] = $allowence->forAuto($budget, $items);

        $resetCarryover = $carryover->autoPrompt($budget);

        return view('auto.index', compact('budget', 'items', 'fields', 'daysDiff', 'sub1', 'resetCarryover', 'warnDaily'));
    }

    public function applyResetCarryover(ResetCarryoverService $carryover, DailyAllowenceService $allowence)
    {
        $budget = AutoBudget::where('user_id', Auth::id())->firstOrFail();

        $carryover->applyAuto($budget, request()->boolean('apply'));

        $allowence->clear();

        return redirect()->route('auto.index');
    }

    public function change($id)
    {
        $budget = AutoBudget::where('user_id', Auth::id())->findOrFail($id);
        $currencies = Storage::json('currencies.json');
        $fields = AutoBudgetField::where('auto_budget_id', $budget->id)->orderBy('id')->get();
        $budgetAmount = $budget->budget_amount;

        return view('auto.change', compact('currencies', 'id', 'fields', 'budgetAmount', 'budget'));
    }

    public function edit(AutoBudgetEditRequest $request, AutoBudget $budget, DailyAllowenceService $allowence)
    {
        $allowence->clear();

        $budget->update([
            'budget_amount' => $request->budget,
            'currency' => $request->currency,
            'reset_date' => $request->reset_date,
            'reset_carry_answered_on' => (int) $budget->reset_date === (int) $request->reset_date
                ? $budget->reset_carry_answered_on
                : null,
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

        return redirect()->route('auto.index');
    }

    public function convert(AutoBudgetEditRequest $request, DailyAllowenceService $allowence)
    {
        $customBudget = CustomBudget::where('user_id', Auth::id())->first();

        if (!$customBudget) {
            return back()->withErrors(['budget' => 'No custom budget to convert.']);
        }

        $allowence->clear();

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

    public function history(AutoBudget $budget, BudgetHistoryService $history)
    {
        abort_unless($budget->user_id === Auth::id(), 403);

        ['months' => $months, 'chartMonths' => $chartMonths] = $history->summarizeAuto($budget->user_id);

        return view('auto.history', compact('budget', 'months', 'chartMonths'));
    }
}
