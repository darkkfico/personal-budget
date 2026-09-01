<?php

namespace Tests\Feature;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetItem;
use App\Models\CustomBudgetItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConvertAutoToCustomTest extends TestCase
{
    use RefreshDatabase;

    public function test_matching_section_names_in_order_keep_auto_items(): void
    {
        [$user, $budget] = $this->makeAutoBudgetWithMilk();

        $this->actingAs($user)
            ->withSession(['type' => 'auto', 'budget_id' => $budget->id])
            ->patch(route('custom.convert'), [
                'budget' => 10000,
                'currency' => 'MKD',
                'reset_date' => 1,
                'custom-field1' => 'Groceries',
                'custom-field1-amount' => 50,
                'custom-field2' => 'Wishes',
                'custom-field2-amount' => 30,
                'custom-field3' => 'Savings',
                'custom-field3-amount' => 20,
            ])
            ->assertRedirect(route('custom.index'));

        $this->assertDatabaseHas('custom_budget_items', [
            'item_name' => 'Milk',
            'item_amount' => 100,
            'field_name' => 'Groceries',
        ]);
        $this->assertDatabaseMissing('auto_budgets', ['id' => $budget->id]);
    }

    public function test_renamed_sections_do_not_keep_auto_items(): void
    {
        [$user, $budget] = $this->makeAutoBudgetWithMilk();

        $this->actingAs($user)
            ->withSession(['type' => 'auto', 'budget_id' => $budget->id])
            ->patch(route('custom.convert'), [
                'budget' => 10000,
                'currency' => 'MKD',
                'reset_date' => 1,
                'custom-field1' => 'Food',
                'custom-field1-amount' => 50,
                'custom-field2' => 'Fun',
                'custom-field2-amount' => 30,
                'custom-field3' => 'Save',
                'custom-field3-amount' => 20,
            ])
            ->assertRedirect(route('custom.index'));

        $this->assertSame(0, CustomBudgetItem::count());
        $this->assertDatabaseMissing('auto_budgets', ['id' => $budget->id]);
    }

    private function makeAutoBudgetWithMilk(): array
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'convert-auto@example.com',
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

        AutoBudgetItem::create([
            'auto_budget_field_id' => $groceries->id,
            'field_name' => 'Groceries',
            'item_name' => 'Milk',
            'item_amount' => 100,
        ]);

        return [$user, $budget];
    }
}
