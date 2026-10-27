<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_provides_distinct_outputs_for_different_queries(): void
    {
        $user = User::factory()->create();
        $chatbot = app(ChatbotService::class);

        $replyGold = $chatbot->ask($user, 'Should I invest in gold?')->response;
        $replySave = $chatbot->ask($user, 'How can I save more money?')->response;
        $replyStocks = $chatbot->ask($user, 'Tell me about stock market investing')->response;
        $replyHello = $chatbot->ask($user, 'Hello')->response;

        // Ensure responses are not the same generic string
        $this->assertNotEquals($replyGold, $replySave);
        $this->assertNotEquals($replySave, $replyStocks);
        $this->assertNotEquals($replyGold, $replyHello);

        // Verify topic-specific keywords
        $this->assertStringContainsStringIgnoringCase('gold', $replyGold);
        $this->assertStringContainsStringIgnoringCase('saving', $replySave);
        $this->assertStringContainsStringIgnoringCase('stock', $replyStocks);
    }

    public function test_user_can_log_expense_naturally_in_chat(): void
    {
        $user = User::factory()->create();
        $chatbot = app(ChatbotService::class);

        $reply = $chatbot->ask($user, '5000 as petrol expense')->response;

        $this->assertDatabaseHas('expenses', [
            'user_id'  => $user->id,
            'category' => 'Petrol',
            'amount'   => 5000,
        ]);
        $this->assertStringContainsString('Petrol', $reply);
    }

    public function test_user_can_log_income_in_chat(): void
    {
        $user = User::factory()->create();
        $chatbot = app(ChatbotService::class);

        $reply = $chatbot->ask($user, 'add Rs 45000 salary income')->response;

        $this->assertDatabaseHas('incomes', [
            'user_id' => $user->id,
            'source'  => 'Salary',
            'amount'  => 45000,
        ]);
        $this->assertStringContainsString('Salary', $reply);
    }

    public function test_user_can_log_investment_in_chat(): void
    {
        $user = User::factory()->create();
        $chatbot = app(ChatbotService::class);

        $reply1 = $chatbot->ask($user, 'add investment 50000 in Mutual Funds')->response;

        $this->assertDatabaseHas('investments', [
            'user_id'         => $user->id,
            'investment_type' => 'Mutual Funds',
            'amount_invested' => 50000,
        ]);
        $this->assertStringContainsString('Mutual Funds', $reply1);

        // Test with typo 'addd'
        $reply2 = $chatbot->ask($user, 'addd 100000 to investment')->response;
        $this->assertDatabaseHas('investments', [
            'user_id'         => $user->id,
            'investment_type' => 'Mutual Funds',
            'amount_invested' => 100000,
        ]);
        $this->assertStringContainsString('100,000.00', $reply2);

        // Test with 'as investment for equity'
        $reply3 = $chatbot->ask($user, 'add 100000 as investment for equity')->response;
        $this->assertDatabaseHas('investments', [
            'user_id'         => $user->id,
            'investment_type' => 'Equity',
            'amount_invested' => 100000,
        ]);
        $this->assertStringContainsString('Equity', $reply3);
        $this->assertStringContainsString('100,000.00', $reply3);
    }

    public function test_user_can_create_goal_in_chat(): void
    {
        $user = User::factory()->create();
        $chatbot = app(ChatbotService::class);

        $reply = $chatbot->ask($user, 'create goal 50000 for emergency fund')->response;

        $this->assertDatabaseHas('goals', [
            'user_id'       => $user->id,
            'title'         => 'Emergency fund',
            'target_amount' => 50000,
        ]);
        $this->assertStringContainsString('Emergency fund', $reply);
    }

    public function test_chat_controller_returns_json_for_ajax_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/chat', [
            'message' => 'What is an emergency fund?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'response',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertStringContainsStringIgnoringCase('emergency', $response->json('response'));
    }
}
