<?php

namespace Tests\Feature;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetItem;
use App\Models\User;
use App\Services\ResetCarryoverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetLeftoverCelebrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_command_keeps_last_month_leftover_after_zeroing_items(): void
    {
        $this->travelTo('2026-05-01 08:00:00');
        [$budget, $groceries, $item] = $this->makeAutoBudget();

        $this->artisan('app:reset-items-amount-auto-budget');

        $this->assertSame(0, (int) $item->fresh()->item_amount);
        $this->assertEquals(7000, $budget->fresh()->pending_reset_leftover);
    }

    public function test_leftover_is_added_to_the_chosen_section_and_budget(): void
    {
        $this->travelTo('2026-05-01 08:00:00');
        [$budget, $groceries] = $this->makeAutoBudget();
        $user = $budget->user;

        $this->artisan('app:reset-items-amount-auto-budget');

        $this->actingAs($user)
            ->post(route('auto.resetCarryover'), ['section_id' => $groceries->id])
            ->assertRedirect(route('auto.index'));

        $budget->refresh();
        $groceries->refresh();

        $this->assertEquals(17000, $budget->budget_amount);
        $this->assertEquals(12000, $groceries->field_amount);
        $this->assertNull($budget->pending_reset_leftover);
    }

    public function test_prompt_is_shown_on_reset_day_when_there_is_leftover(): void
    {
        $this->travelTo('2026-05-01 08:00:00');
        [$budget] = $this->makeAutoBudget();

        $prompt = app(ResetCarryoverService::class)->autoPrompt($budget->fresh(['autoBudgetFields']));

        $this->assertNotNull($prompt);
        $this->assertEquals(7000, $prompt['amount']);
        $this->assertEquals(17000, $prompt['new_budget']);
    }

    /**
     * @return array{0: AutoBudget, 1: AutoBudgetField, 2: AutoBudgetItem}
     */
    private function makeAutoBudget(): array
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'reset-leftover@example.com',
            'password' => 'password',
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
        ]);

        $groceries = AutoBudgetField::create([
            'auto_budget_id' => $budget->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
        ]);

        AutoBudgetField::create([
            'auto_budget_id' => $budget->id,
            'field_name' => 'Wishes',
            'field_amount' => 3000,
        ]);

        AutoBudgetField::create([
            'auto_budget_id' => $budget->id,
            'field_name' => 'Savings',
            'field_amount' => 2000,
        ]);

        $item = AutoBudgetItem::create([
            'auto_budget_field_id' => $groceries->id,
            'field_name' => 'Groceries',
            'item_name' => 'Milk',
            'item_amount' => 1000,
        ]);

        return [$budget, $groceries, $item];
    }
}
