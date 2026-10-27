<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ImpulseItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpulseShieldTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_impulse_shield(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/impulse-shield');

        $response->assertStatus(200);
        $response->assertSee('The Impulse Shield');
        $response->assertSee('Cooldown Vault');
        $response->assertSee('Victory Hall of Fame');
    }

    public function test_user_can_lock_an_impulse_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/impulse-shield', [
            'item_name' => 'Sony WH-1000XM5 Headphones',
            'price' => 29990,
            'category' => 'Shopping',
            'notes' => 'Saw a flash discount sale, really want it',
        ]);

        $response->assertRedirect('/impulse-shield');
        $this->assertDatabaseHas('impulse_items', [
            'user_id' => $user->id,
            'item_name' => 'Sony WH-1000XM5 Headphones',
            'price' => 29990,
            'status' => 'cooling',
        ]);

        $item = ImpulseItem::where('user_id', $user->id)->first();
        $this->assertNotNull($item->cooldown_until);
        $this->assertTrue(Carbon::parse($item->cooldown_until)->isFuture());
    }

    public function test_user_can_resist_an_impulse_item(): void
    {
        $user = User::factory()->create();
        $item = ImpulseItem::create([
            'user_id' => $user->id,
            'item_name' => 'Mechanical Keyboard',
            'price' => 12000,
            'category' => 'Shopping',
            'cooldown_until' => Carbon::now()->addHours(40),
            'status' => 'cooling',
        ]);

        $response = $this->actingAs($user)->post("/impulse-shield/{$item->id}/resist");

        $response->assertRedirect('/impulse-shield');
        $this->assertDatabaseHas('impulse_items', [
            'id' => $item->id,
            'status' => 'resisted',
        ]);
    }

    public function test_user_can_buy_item_and_it_creates_expense(): void
    {
        $user = User::factory()->create();
        $item = ImpulseItem::create([
            'user_id' => $user->id,
            'item_name' => 'Designer Sneakers',
            'price' => 8500,
            'category' => 'Shopping',
            'cooldown_until' => Carbon::now()->subMinutes(10), // expired cooldown
            'status' => 'cooling',
        ]);

        $response = $this->actingAs($user)->post("/impulse-shield/{$item->id}/buy");

        $response->assertRedirect('/impulse-shield');
        $this->assertDatabaseHas('impulse_items', [
            'id' => $item->id,
            'status' => 'bought',
        ]);

        // Check Expense record was generated
        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'amount' => 8500,
            'category' => 'Shopping',
        ]);
    }

    public function test_sms_parser_api_extracts_transaction_details(): void
    {
        $user = User::factory()->create();

        $rawSms = 'Sent Rs. 450.00 from HDFC Bank A/C **1234 to SWIGGY on 20-Oct-26 via UPI. Ref 123456789.';

        $response = $this->actingAs($user)->postJson('/api/parse-sms', [
            'sms_text' => $rawSms,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'amount' => 450,
            'merchant' => 'SWIGGY',
            'category' => 'Food & Dining',
        ]);
    }

    public function test_sms_parser_handles_unparseable_text_gracefully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/parse-sms', [
            'sms_text' => 'Good morning! Have a wonderful day ahead.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
        ]);
    }
}
