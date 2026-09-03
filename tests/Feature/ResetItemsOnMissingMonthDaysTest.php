<?php

namespace Tests\Feature;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetItem;
use App\Models\CustomBudget;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetItemsOnMissingMonthDaysTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_items_reset_on_the_first_when_april_has_no_31st(): void
    {
        [$budget, $item] = $this->makeAutoBudget(31, 250);

        $this->travelTo('2026-05-01 08:00:00');
        $this->artisan('app:reset-items-amount-auto-budget');

        $this->assertSame(0, $item->fresh()->item_amount);
        $this->assertSame(31, $budget->fresh()->reset_date);
    }

    public function test_auto_items_do_not_reset_on_april_30_for_a_31_reset_date(): void
    {
        [, $item] = $this->makeAutoBudget(31, 250);

        $this->travelTo('2026-04-30 08:00:00');
        $this->artisan('app:reset-items-amount-auto-budget');

        $this->assertSame(250, $item->fresh()->item_amount);
    }

    public function test_auto_items_reset_on_march_first_when_february_has_no_30th(): void
    {
        [$budget, $item] = $this->makeAutoBudget(30, 80);

        $this->travelTo('2026-03-01 08:00:00');
        $this->artisan('app:reset-items-amount-auto-budget');

        $this->assertSame(0, $item->fresh()->item_amount);
        $this->assertSame(30, $budget->fresh()->reset_date);
    }

    public function test_custom_items_reset_on_the_first_when_the_month_lacks_the_reset_day(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'custom-overflow-reset@example.com',
            'password' => 'password',
        ]);

        $budget = CustomBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 31,
        ]);

        $field = CustomBudgetField::create([
            'custom_budget_id' => $budget->id,
            'field_name' => 'Food',
            'field_amount' => 5000,
        ]);

        $item = CustomBudgetItem::create([
            'custom_budget_field_id' => $field->id,
            'field_name' => 'Food',
            'item_name' => 'Bread',
            'item_amount' => 40,
        ]);

        $this->travelTo('2026-05-01 08:00:00');
        $this->artisan('app:reset-items-amount-custom-budget');

        $this->assertSame(0, $item->fresh()->item_amount);
        $this->assertSame(31, $budget->fresh()->reset_date);
    }

    /**
     * @return array{0: AutoBudget, 1: AutoBudgetItem}
     */
    private function makeAutoBudget(int $resetDate, int $itemAmount): array
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'auto-overflow-reset-'.$resetDate.'-'.$itemAmount.'@example.com',
            'password' => 'password',
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => $resetDate,
        ]);

        $field = AutoBudgetField::create([
            'auto_budget_id' => $budget->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
        ]);

        $item = AutoBudgetItem::create([
            'auto_budget_field_id' => $field->id,
            'field_name' => 'Groceries',
            'item_name' => 'Milk',
            'item_amount' => $itemAmount,
        ]);

        return [$budget, $item];
    }
}
