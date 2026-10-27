<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Investment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JarvisController extends Controller
{
    /**
     * Handle real-time voice query from the browser.
     */
    public function query(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transcript' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $transcript = trim($validated['transcript']);

        // Gather real user telemetry
        $totalIncome   = (float) $user->incomes()->sum('amount');
        $totalExpenses = (float) $user->expenses()->sum('amount');
        $netBalance    = $totalIncome - $totalExpenses;

        $thisMonth = Carbon::now()->startOfMonth();
        $monthExpenses = (float) $user->expenses()->where('expense_date', '>=', $thisMonth)->sum('amount');
        $monthIncome   = (float) $user->incomes()->where('income_date', '>=', $thisMonth)->sum('amount');
        $investmentsVal = (float) $user->investments()->sum('current_value');

        $topExpense = $user->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $context = <<<CTX
User: {$user->name}
Net Liquid Cash Balance: Rs {$netBalance}
This Month Income: Rs {$monthIncome}
This Month Expenses: Rs {$monthExpenses}
Top Spending Category: {$topExpense?->category} (Rs {$topExpense?->total})
Total Investments: Rs {$investmentsVal}
User Spoken Query: "{$transcript}"
CTX;

        $apiKey = config('services.gemini.api_key');
        $responseText = null;

        if ($apiKey) {
            try {
                $systemPrompt = <<<SYS
You are JARVIS CFO, an ultra-smart, sophisticated, analytical voice assistant for FinSight AI.
The user is speaking to you directly through voice.
Rules:
1. Answer in under 40 words so it sounds natural when spoken aloud.
2. If the user asks whether they can afford an item/expense, give a direct verdict (e.g., "Yes, you can comfortably afford it", or "That would stretch your reserves").
3. Use the user's actual balance (Rs {$netBalance}) and monthly burn rate.
4. All amounts are in Indian Rupees (Rs).
5. Never use markdown formatting (no asterisks, bullet points, hashes) because your output is read by text-to-speech.
SYS;

                $apiResponse = Http::timeout(20)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'systemInstruction' => [
                            'parts' => [['text' => $systemPrompt]],
                        ],
                        'contents' => [
                            ['parts' => [['text' => $context]]],
                        ],
                    ]
                );

                if ($apiResponse->successful()) {
                    $raw = data_get($apiResponse->json(), 'candidates.0.content.parts.0.text');
                    if ($raw) {
                        $responseText = trim(str_replace(['*', '#', '`'], '', $raw));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('JarvisController: Voice query error — ' . $e->getMessage());
            }
        }

        // Fallback heuristic if API is unreachable
        if (!$responseText) {
            $responseText = $this->fallbackJarvisReply($transcript, $netBalance, $monthExpenses);
        }

        return response()->json([
            'success'      => true,
            'transcript'   => $transcript,
            'speech_text'  => $responseText,
            'display_text' => $responseText,
            'net_balance'  => $netBalance,
            'month_spend'  => $monthExpenses,
            'timestamp'    => now()->format('H:i:s'),
        ]);
    }

    protected function fallbackJarvisReply(string $transcript, float $netBalance, float $monthExpenses): string
    {
        $lower = strtolower($transcript);

        if (preg_match('/(?:afford|buy|spend|get)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*)/i', $transcript, $m)) {
            $askAmount = (float) str_replace(',', '', $m[1]);
            if ($askAmount <= $netBalance * 0.25) {
                return "Yes, you can comfortably afford that Rs " . number_format($askAmount) . " expense. Your available liquid cash is Rs " . number_format($netBalance) . ".";
            } else {
                return "I would advise caution. A Rs " . number_format($askAmount) . " purchase consumes over 25% of your Rs " . number_format($netBalance) . " liquid reserves.";
            }
        }

        if (str_contains($lower, 'balance') || str_contains($lower, 'money') || str_contains($lower, 'cash')) {
            return "Your current net cash balance stands at Rs " . number_format($netBalance) . ", with Rs " . number_format($monthExpenses) . " spent this month.";
        }

        if (str_contains($lower, 'runway') || str_contains($lower, 'quit') || str_contains($lower, 'job')) {
            $burn = max(1000, $monthExpenses);
            $months = round($netBalance / $burn, 1);
            return "Based on your current monthly spend of Rs " . number_format($burn) . ", your liquid cash gives you approximately {$months} months of survival runway.";
        }

        return "FinSight telemetry is active. With Rs " . number_format($netBalance) . " in reserves, your cash flow is stable. How can I assist your financial decisions today?";
    }
}
