<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_investments_index_with_kpi_metrics(): void
    {
        $user = User::factory()->create();

        $user->investments()->create([
            'investment_type' => 'Mutual Funds',
            'name' => 'Parag Parikh Flexi Cap Fund',
            'amount_invested' => 100000.00,
            'current_value' => 125000.00,
            'investment_date' => now()->subMonths(6),
        ]);

        $user->investments()->create([
            'investment_type' => 'Gold',
            'name' => 'Sovereign Gold Bond (SGB)',
            'amount_invested' => 50000.00,
            'current_value' => 58000.00,
            'investment_date' => now()->subMonths(3),
        ]);

        $response = $this->actingAs($user)->get(route('investments.index'));

        $response->assertStatus(200);
        $response->assertSee('Investment Portfolio');
        $response->assertSee('Total Invested');
        $response->assertSee('Portfolio Valuation');
        $response->assertSee('Parag Parikh Flexi Cap Fund');
        $response->assertSee('Sovereign Gold Bond (SGB)');
        $response->assertSee('Rs 150,000.00'); // Total invested
        $response->assertSee('Rs 183,000.00'); // Portfolio valuation
        $response->assertSee('Rs 33,000.00');  // Unrealized gain
    }

    public function test_empty_investments_shows_friendly_empty_state(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('investments.index'));

        $response->assertStatus(200);
        $response->assertSee('No investments recorded yet');
        $response->assertSee('Add First Investment');
    }
}
