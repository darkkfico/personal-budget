<?php

namespace Tests\Feature;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\User;
use App\Services\OnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingResumeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_resumes_auto_budget_form_after_type_was_chosen(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'resume-auto@example.com',
            'password' => 'Password1!',
            'onboarding_step' => OnboardingService::AUTO_FORM,
        ]);

        $this->post(route('login'), [
            'email' => 'resume-auto@example.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('auto.form'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_resumes_custom_budget_form_after_type_was_chosen(): void
    {
        User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'resume-custom@example.com',
            'password' => 'Password1!',
            'onboarding_step' => OnboardingService::CUSTOM_FORM,
        ]);

        $this->post(route('login'), [
            'email' => 'resume-custom@example.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('custom.form'));
    }

    public function test_login_with_finished_auto_budget_goes_to_budget_index(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'done-auto@example.com',
            'password' => 'Password1!',
            'onboarding_step' => OnboardingService::COMPLETE,
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
        ]);

        AutoBudgetField::create([
            'auto_budget_id' => $budget->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
        ]);

        $this->post(route('login'), [
            'email' => 'done-auto@example.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('auto.index'));
    }

    public function test_register_then_login_with_same_credentials_works(): void
    {
        $this->post(route('auth.store'), [
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'same-user@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertRedirect(route('start.start'));

        $this->get(route('auth.logout'));

        $this->post(route('login'), [
            'email' => 'same-user@example.com',
            'password' => 'Password1!',
        ])->assertRedirect(route('start.start'));
    }
}
