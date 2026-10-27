<?php

namespace Tests\Feature;

use App\Models\FinancialTimeline;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiverseTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_multiverse(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/multiverse');

        $response->assertStatus(200);
        $response->assertSee('Financial Multiverse');
        $response->assertSee('branches:');
        $response->assertSee('git diff reality/main');
        $response->assertSee('Future Self (Digital Twin)');
        $response->assertSee('Merge to Reality');
    }

    public function test_default_presets_are_automatically_provisioned(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/multiverse');

        $this->assertDatabaseHas('financial_timelines', [
            'user_id' => $user->id,
            'slug'    => 'fire-at-42',
        ]);
        $this->assertDatabaseHas('financial_timelines', [
            'user_id' => $user->id,
            'slug'    => 'startup-leap',
        ]);
        $this->assertDatabaseHas('financial_timelines', [
            'user_id' => $user->id,
            'slug'    => 'mega-real-estate',
        ]);
    }

    public function test_user_can_fork_custom_timeline_branch(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/multiverse/fork', [
            'name'                    => 'Digital Nomad at 35',
            'income_multiplier'       => 1.30,
            'expense_multiplier'      => 0.70,
            'lump_sum_inflow'         => 100000,
            'lump_sum_outflow'        => 50000,
            'monthly_investment_rate' => 50,
            'market_cagr'             => 14,
            'target_horizon_years'    => 15,
            'description'             => 'Relocate to lower-cost hub with remote income.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('financial_timelines', [
            'user_id'           => $user->id,
            'name'              => 'Digital Nomad at 35',
            'income_multiplier' => 1.30,
            'expense_multiplier'=> 0.70,
        ]);
    }

    public function test_future_self_chat_api_returns_ai_dialogue(): void
    {
        $user = User::factory()->create();
        $timeline = FinancialTimeline::create([
            'user_id'                 => $user->id,
            'name'                    => 'Freedom at 40',
            'slug'                    => 'freedom-at-40',
            'income_multiplier'       => 1.25,
            'expense_multiplier'      => 0.75,
            'monthly_investment_rate' => 40,
            'market_cagr'             => 12,
            'target_horizon_years'    => 15,
            'status'                  => 'simulated',
        ]);

        $response = $this->actingAs($user)->postJson("/api/multiverse/{$timeline->id}/future-self-chat", [
            'message' => 'What was the hardest trade-off in the early years?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'reply',
            'timeline',
            'freedom_age',
            'delta_10y',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('reply'));
    }

    public function test_user_can_merge_timeline_into_active_reality(): void
    {
        $user = User::factory()->create();
        $timeline = FinancialTimeline::create([
            'user_id'                 => $user->id,
            'name'                    => 'Lean FIRE 42',
            'slug'                    => 'lean-fire-42',
            'income_multiplier'       => 1.15,
            'expense_multiplier'      => 0.80,
            'monthly_investment_rate' => 45,
            'market_cagr'             => 12,
            'target_horizon_years'    => 15,
            'status'                  => 'simulated',
        ]);

        $response = $this->actingAs($user)->post("/multiverse/{$timeline->id}/merge");

        $response->assertRedirect();
        $this->assertDatabaseHas('financial_timelines', [
            'id'     => $timeline->id,
            'status' => 'merged',
        ]);
        $this->assertNotNull($timeline->fresh()->merged_at);

        // Check that a strategic Goal was provisioned
        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'title'   => "Multiverse Goal: {$timeline->name}",
        ]);

        // Check that budgets exist
        $this->assertGreaterThan(0, $user->fresh()->budgets()->count());
    }

    public function test_user_can_delete_custom_timeline_branch(): void
    {
        $user = User::factory()->create();
        $timeline = FinancialTimeline::create([
            'user_id'                 => $user->id,
            'name'                    => 'Temporary Experiment',
            'slug'                    => 'temp-exp',
            'income_multiplier'       => 1.0,
            'expense_multiplier'      => 1.0,
            'monthly_investment_rate' => 20,
            'market_cagr'             => 10,
            'target_horizon_years'    => 10,
        ]);

        $response = $this->actingAs($user)->delete("/multiverse/{$timeline->id}");

        $response->assertRedirect('/multiverse');
        $this->assertDatabaseMissing('financial_timelines', [
            'id' => $timeline->id,
        ]);
    }
}
