<?php

namespace App\Http\Controllers;

use App\Models\ChatHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $history = auth()->user()->chatHistories()->latest('created_at')->take(8)->get();

        return view('chat.index', compact('history'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = trim($validated['message']);

        // Gather user's financial profile context for Gemini API
        $user = auth()->user();
        $totalIncome = $user->incomes()->sum('amount');
        $totalExpenses = $user->expenses()->sum('amount');
        $netSavings = $totalIncome - $totalExpenses;

        $budgets = $user->budgets()->get()->map(function ($b) {
            return "- {$b->category} (Limit: \${$b->amount_limit})";
        })->implode("\n");

        $goals = $user->goals()->get()->map(function ($g) {
            return "- {$g->name} (Target: \${$g->target_amount}, Current: \${$g->current_amount})";
        })->implode("\n");

        $investments = $user->investments()->get()->map(function ($i) {
            return "- {$i->name} ({$i->investment_type}, Invested: \${$i->amount_invested}, Current: \${$i->current_value})";
        })->implode("\n");

        $financialProfile = "
User Financial Profile:
- Total Income: \${$totalIncome}
- Total Expenses: \${$totalExpenses}
- Net Savings/Balance: \${$netSavings}

Active Budgets:
" . ($budgets ?: "None") . "

Active Financial Goals:
" . ($goals ?: "None") . "

Active Investments:
" . ($investments ?: "None") . "

User Question: {$message}
";

        $apiKey = config('services.gemini.api_key');
        $responseContent = null;

        if ($apiKey) {
            try {
                $apiResponse = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => 'You are a professional financial advisor for FinSight AI. Use the user\'s financial profile context (income, expenses, budgets, goals, investments) provided in the prompt to give highly personalized, concise, actionable financial advice. Keep your response under 150 words.']
                        ]
                    ],
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $financialProfile]
                            ]
                        ]
                    ]
                ]);

                if ($apiResponse->successful()) {
                    $data = $apiResponse->json();
                    $responseContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                }
            } catch (\Exception $e) {
                // Log/fail gracefully
            }
        }

        if (!$responseContent) {
            // Intelligent local fallback using user's real balance
            $responseContent = match (true) {
                str_contains(strtolower($message), 'budget') => "Based on your active budgets, a strong financial plan starts with keeping within your limits. Try categorizing your highest expenses first to stay on track.",
                str_contains(strtolower($message), 'invest') => "Based on your active investments, you should consider diversifying your portfolio across different assets to manage risks and improve returns.",
                str_contains(strtolower($message), 'goal') => "Based on your goals, consider setting aside a regular monthly portion of your net savings (\$" . number_format($netSavings, 2) . ") to achieve them sooner.",
                default => "Thank you for asking! With a current net savings of \$" . number_format($netSavings, 2) . ", we recommend reviewing your budgets weekly to optimize your financial growth.",
            };
        }

        auth()->user()->chatHistories()->create([
            'message' => $message,
            'response' => trim($responseContent),
        ]);

        return back()->with('status', 'Your message was sent successfully.');
    }
}

