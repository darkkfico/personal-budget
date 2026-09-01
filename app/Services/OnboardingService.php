<?php

namespace App\Services;

use App\Models\AutoBudget;
use App\Models\CustomBudget;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class OnboardingService
{
    public const CHOOSE_TYPE = 'choose_type';

    public const AUTO_FORM = 'auto_form';

    public const CUSTOM_FORM = 'custom_form';

    public const CUSTOM_NON_RESETABLE = 'custom_non_resetable';

    public const COMPLETE = 'complete';

    public function mark(User $user, string $step): void
    {
        $user->update(['onboarding_step' => $step]);

        if ($step === self::AUTO_FORM) {
            session(['type' => 'auto']);
        }

        if (in_array($step, [self::CUSTOM_FORM, self::CUSTOM_NON_RESETABLE], true)) {
            session(['type' => 'custom']);
        }
    }

    public function redirect(User $user): RedirectResponse
    {
        if (AutoBudget::where('user_id', $user->id)->exists()) {
            session(['type' => 'auto']);
            $this->markIfIncomplete($user, self::COMPLETE);

            return redirect()->route('auto.index');
        }

        if (CustomBudget::where('user_id', $user->id)->exists()) {
            session(['type' => 'custom']);

            if ($user->onboarding_step === self::CUSTOM_NON_RESETABLE) {
                return redirect()->route('custom.formNonResetable');
            }

            $this->markIfIncomplete($user, self::COMPLETE);

            return redirect()->route('custom.index');
        }

        return match ($user->onboarding_step) {
            self::AUTO_FORM => redirect()->route('auto.form'),
            self::CUSTOM_FORM => redirect()->route('custom.form'),
            default => redirect()->route('start.start'),
        };
    }

    private function markIfIncomplete(User $user, string $step): void
    {
        if ($user->onboarding_step !== $step) {
            $user->update(['onboarding_step' => $step]);
        }
    }
}
