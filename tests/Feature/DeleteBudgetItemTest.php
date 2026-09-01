<?php

namespace Tests\Feature;

use App\Models\AutoBudget;
use App\Models\AutoBudgetField;
use App\Models\AutoBudgetFieldSnapshot;
use App\Models\AutoBudgetItem;
use App\Models\AutoBudgetItemSnapshot;
use App\Models\AutoBudgetSnapshot;
use App\Models\CustomBudget;
use App\Models\CustomBudgetField;
use App\Models\CustomBudgetItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBudgetItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_item_delete_removes_the_item(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'auto-delete@example.com',
            'password' => 'password',
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
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
            'item_amount' => 100,
        ]);

        $this->actingAs($user)
            ->withSession(['type' => 'auto', 'budget_id' => $budget->id])
            ->delete(route('auto.item.delete', ['item' => $item->id]))
            ->assertRedirect(route('auto.index'));

        $this->assertDatabaseMissing('auto_budget_items', ['id' => $item->id]);
    }

    public function test_custom_item_delete_removes_the_item(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'custom-delete@example.com',
            'password' => 'password',
        ]);

        $budget = CustomBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
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
            'item_amount' => 50,
        ]);

        $this->actingAs($user)
            ->withSession(['type' => 'custom', 'budget_id' => $budget->id])
            ->delete(route('custom.item.delete', ['item' => $item->id]))
            ->assertRedirect(route('custom.index'));

        $this->assertDatabaseMissing('custom_budget_items', ['id' => $item->id]);
    }

    public function test_auto_index_delete_form_posts_to_the_auto_delete_route(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'auto-form@example.com',
            'password' => 'password',
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
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
            'item_amount' => 100,
        ]);

        $html = $this->actingAs($user)
            ->withSession(['budget_id' => $budget->id])
            ->get(route('auto.index'))
            ->assertOk()
            ->getContent();

        $expected = htmlspecialchars(route('auto.item.delete', ['item' => $item->id]), ENT_QUOTES);

        $this->assertStringContainsString($expected, $html);
        $this->assertStringContainsString('data-delete-open', $html);
        $this->assertStringContainsString('id="itemDeletePopup"', $html);
    }

    public function test_permanent_delete_removes_the_item_snapshot(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'auto-permanent@example.com',
            'password' => 'password',
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
        ]);

        $snapshot = AutoBudgetSnapshot::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        $field = AutoBudgetField::create([
            'auto_budget_id' => $budget->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
        ]);

        $fieldSnapshot = AutoBudgetFieldSnapshot::create([
            'auto_budget_snapshot_id' => $snapshot->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        $item = AutoBudgetItem::create([
            'auto_budget_field_id' => $field->id,
            'field_name' => 'Groceries',
            'item_name' => 'Milk',
            'item_amount' => 100,
        ]);

        $itemSnapshot = AutoBudgetItemSnapshot::create([
            'auto_budget_field_snapshot_id' => $fieldSnapshot->id,
            'auto_budget_item_id' => $item->id,
            'field_name' => 'Groceries',
            'item_name' => 'Milk',
            'item_amount' => 100,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        $this->actingAs($user)
            ->delete(route('auto.item.delete', ['item' => $item->id]), ['permanent' => '1'])
            ->assertRedirect(route('auto.index'));

        $this->assertDatabaseMissing('auto_budget_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('auto_budget_item_snapshots', ['id' => $itemSnapshot->id]);
    }

    public function test_non_permanent_delete_keeps_the_item_snapshot(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'auto-keep@example.com',
            'password' => 'password',
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
        ]);

        $snapshot = AutoBudgetSnapshot::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        $field = AutoBudgetField::create([
            'auto_budget_id' => $budget->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
        ]);

        $fieldSnapshot = AutoBudgetFieldSnapshot::create([
            'auto_budget_snapshot_id' => $snapshot->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        $item = AutoBudgetItem::create([
            'auto_budget_field_id' => $field->id,
            'field_name' => 'Groceries',
            'item_name' => 'Milk',
            'item_amount' => 100,
        ]);

        $itemSnapshot = AutoBudgetItemSnapshot::create([
            'auto_budget_field_snapshot_id' => $fieldSnapshot->id,
            'auto_budget_item_id' => $item->id,
            'field_name' => 'Groceries',
            'item_name' => 'Milk',
            'item_amount' => 100,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        $this->actingAs($user)
            ->delete(route('auto.item.delete', ['item' => $item->id]), ['permanent' => '0'])
            ->assertRedirect(route('auto.index'));

        $this->assertDatabaseMissing('auto_budget_items', ['id' => $item->id]);
        $this->assertDatabaseHas('auto_budget_item_snapshots', ['id' => $itemSnapshot->id]);

        $this->actingAs($user)
            ->get(route('auto.history', $budget))
            ->assertOk()
            ->assertSee('Milk')
            ->assertSee('100');
    }

    public function test_history_lists_item_snapshots_in_the_month_they_were_taken(): void
    {
        $user = User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'auto-history-month@example.com',
            'password' => 'password',
        ]);

        $budget = AutoBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
        ]);

        $snapshot = AutoBudgetSnapshot::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
            'snapshot' => now()->subMonth(),
            'month' => now()->subMonth()->format('n'),
        ]);

        $fieldSnapshot = AutoBudgetFieldSnapshot::create([
            'auto_budget_snapshot_id' => $snapshot->id,
            'field_name' => 'Groceries',
            'field_amount' => 5000,
            'snapshot' => now()->subMonth(),
            'month' => now()->subMonth()->format('n'),
        ]);

        AutoBudgetItemSnapshot::create([
            'auto_budget_field_snapshot_id' => $fieldSnapshot->id,
            'auto_budget_item_id' => null,
            'field_name' => 'Groceries',
            'item_name' => 'Kept after delete',
            'item_amount' => 250,
            'snapshot' => now(),
            'month' => now()->format('n'),
        ]);

        $this->actingAs($user)
            ->get(route('auto.history', $budget))
            ->assertOk()
            ->assertSee(now()->format('F Y'))
            ->assertSee('Kept after delete')
            ->assertSee('250');
    }
}
