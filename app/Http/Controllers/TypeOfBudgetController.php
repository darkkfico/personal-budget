<?php

namespace App\Http\Controllers;

use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TypeOfBudgetController extends Controller
{
    public function choose(Request $request, OnboardingService $onboarding)
    {

        $request->validate([
            "type" => ['required', "in:auto,custom"],
        ]);

        $user = Auth::user();

        if ($request->type === "auto") {
            $onboarding->mark($user, OnboardingService::AUTO_FORM);

            return redirect()->route("auto.form");
        }

        $onboarding->mark($user, OnboardingService::CUSTOM_FORM);

        return redirect()->route("custom.form");
    }

}
