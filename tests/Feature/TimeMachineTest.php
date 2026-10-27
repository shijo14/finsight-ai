<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeMachineTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_time_machine(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/time-machine');

        $response->assertStatus(200);
        $response->assertSee('Financial Time Machine');
        $response->assertSee('Simulation Levers');
        $response->assertSee('Monte Carlo Net Worth Projection');
    }

    public function test_jarvis_voice_query_returns_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/jarvis/voice-query', [
            'transcript' => 'Can I afford to buy an iPad for Rs 50000?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'transcript',
            'speech_text',
            'display_text',
            'net_balance',
            'month_spend',
        ]);
    }

    public function test_time_machine_ai_strategy_returns_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/time-machine/ai-strategy', [
            'scenario_name'  => 'Startup Leap',
            'survival_rate'  => 85.5,
            'runway_months'  => 14.2,
            'projected_cash' => 250000,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'scenario',
            'survival',
            'runway',
            'strategy',
        ]);
        $this->assertIsArray($response->json('strategy'));
    }
}
