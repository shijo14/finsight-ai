<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WarRoomDeliberation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZenQuantNexusTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_zen_quant_nexus(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/nexus');

        $response->assertStatus(200);
        $response->assertSee('The Zen & Quant Nexus', false);
        $response->assertSee('Financial Independence Engine');
        $response->assertSee('The 4-D Capital Allocation Radar');
        $response->assertSee('Automated Overnight Cash Sweep');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/nexus');

        $response->assertRedirect('/login');
    }

    public function test_summon_war_room_council_returns_valid_deliberation_and_persists_record(): void
    {
        $user = User::factory()->create();

        $payload = [
            'item_name' => 'Sony WH-1000XM5 Headphones',
            'amount'    => 24990,
            'category'  => 'Gadgets',
            'reason'    => 'Deep focus audio isolation during programming sprints',
        ];

        $response = $this->actingAs($user)->postJson('/api/nexus/war-room', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'item_name',
            'amount',
            'kenji',
            'chen',
            'minjun',
            'vote',
            'ruling',
            'opp_cost_10y',
            'deliberation_id',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('kenji'));
        $this->assertNotEmpty($response->json('chen'));
        $this->assertNotEmpty($response->json('minjun'));
        $this->assertContains($response->json('vote'), ['approved', 'vetoed', 'conditional']);

        $this->assertDatabaseHas('war_room_deliberations', [
            'user_id'   => $user->id,
            'item_name' => 'Sony WH-1000XM5 Headphones',
            'amount'    => 24990,
        ]);
    }

    public function test_live_telemetry_endpoint_returns_escape_velocity_metrics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/nexus/telemetry');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'telemetry' => [
                'monthly_burn',
                'invested_assets',
                'monthly_income',
                'burn_per_sec',
                'yield_per_sec',
                'net_velocity',
                'is_escape',
                'capital_for_escape',
                'capital_gap',
                'escape_percent',
            ],
        ]);
    }

    public function test_dashboard_displays_zen_quant_nexus_spotlight(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Zen &amp; Quant Nexus', false);
        $response->assertSee('Behavioral • Quant • Growth');
    }
}
