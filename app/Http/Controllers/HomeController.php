<?php

namespace App\Http\Controllers;

use App\Models\AutoBudget;
use App\Models\CustomBudget;
use App\Services\OnboardingService;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index()
    {
        return view("home.index");
    }

    public function dashboard(OnboardingService $onboarding)
    {
        return $onboarding->redirect(Auth::user());
    }

    public function start(OnboardingService $onboarding)
    {
        $user = Auth::user();

        if (
            AutoBudget::where('user_id', $user->id)->exists()
            || CustomBudget::where('user_id', $user->id)->exists()
        ) {
            return $onboarding->redirect($user);
        }

        if (in_array($user->onboarding_step, [
            OnboardingService::AUTO_FORM,
            OnboardingService::CUSTOM_FORM,
            OnboardingService::CUSTOM_NON_RESETABLE,
        ], true)) {
            return $onboarding->redirect($user);
        }

        $onboarding->mark($user, OnboardingService::CHOOSE_TYPE);

        return view("start.start");
    }

}
