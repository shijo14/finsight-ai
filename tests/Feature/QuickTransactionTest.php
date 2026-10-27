<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Real-Time Wealth & CFO Dashboard');
        $response->assertSee('Active Monthly Budgets');
        $response->assertSee('Quick Add Transaction');
    }

    public function test_user_can_store_quick_expense(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/quick-transaction', [
            'type'        => 'expense',
            'amount'      => 450.50,
            'category'    => 'Food & Dining',
            'date'        => now()->format('Y-m-d'),
            'description' => 'Test Swiggy order',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('expenses', [
            'user_id'  => $user->id,
            'category' => 'Food & Dining',
            'amount'   => 450.50,
        ]);
    }

    public function test_user_can_store_quick_income(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/quick-transaction', [
            'type'   => 'income',
            'amount' => 75000,
            'source' => 'Consulting Advisory',
            'date'   => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('incomes', [
            'user_id' => $user->id,
            'source'  => 'Consulting Advisory',
            'amount'  => 75000,
        ]);
    }

    public function test_user_can_seed_demo_portfolio(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/demo/seed');

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('incomes', ['user_id' => $user->id]);
        $this->assertDatabaseHas('expenses', ['user_id' => $user->id]);
        $this->assertDatabaseHas('budgets', ['user_id' => $user->id]);
        $this->assertDatabaseHas('goals', ['user_id' => $user->id]);
        $this->assertDatabaseHas('investments', ['user_id' => $user->id]);
    }
}
