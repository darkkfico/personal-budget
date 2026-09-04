<?php

namespace Tests\Feature;

use App\Models\CustomBudget;
use App\Models\CustomBudgetField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeftoverAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_under_allocated_custom_budget_shows_leftover_instead_of_creating(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('custom.store'), $this->payload(80))
            ->assertRedirect()
            ->assertSessionHas('leftover_allocation.amount', 2000)
            ->assertSessionHas('leftover_allocation.sections.1', 'Groceries')
            ->assertSessionMissing('errors');

        $this->assertSame(0, CustomBudget::count());
    }

    public function test_leftover_is_added_to_the_chosen_section(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('custom.store'), $this->payload(80) + ['leftover_section' => 1])
            ->assertRedirect(route('custom.formNonResetable'));

        $this->assertDatabaseHas('custom_budget_fields', [
            'field_name' => 'Groceries',
            'field_amount' => 10000,
        ]);
    }

    public function test_edit_leftover_is_added_to_the_chosen_section(): void
    {
        $user = $this->makeUser();
        $budget = CustomBudget::create([
            'user_id' => $user->id,
            'budget_amount' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
        ]);
        CustomBudgetField::create([
            'custom_budget_id' => $budget->id,
            'field_name' => 'Groceries',
            'field_amount' => 10000,
        ]);

        $this->actingAs($user)
            ->patch(route('custom.edit'), $this->payload(70) + [
                'budget_id' => $budget->id,
                'leftover_section' => 1,
            ])
            ->assertRedirect(route('custom.formNonResetable'));

        $this->assertDatabaseHas('custom_budget_fields', [
            'id' => CustomBudgetField::first()->id,
            'field_amount' => 10000,
        ]);
    }

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Test',
            'lastname' => 'User',
            'email' => 'leftover@example.com',
            'password' => 'password',
        ]);
    }

    private function payload(int $percent): array
    {
        return [
            'budget' => 10000,
            'currency' => 'MKD',
            'reset_date' => 1,
            'custom-field1' => 'Groceries',
            'custom-field1-amount' => $percent,
        ];
    }
}
