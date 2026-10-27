<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Investment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TimeMachineController extends Controller
{
    /**
     * Render the Financial Time Machine simulator.
     */
    public function index(): View
    {
        $userId = auth()->id();
        $user   = auth()->user();

        $totalIncome    = (float) Income::where('user_id', $userId)->sum('amount');
        $totalExpenses  = (float) Expense::where('user_id', $userId)->sum('amount');
        $liquidCash     = max(0, $totalIncome - $totalExpenses);

        // Calculate average monthly metrics
        $twoMonthsAgo = Carbon::now()->subDays(60);
        $recentExpenses = (float) Expense::where('user_id', $userId)
            ->where('expense_date', '>=', $twoMonthsAgo)
            ->sum('amount');
        $monthlyBurn = $recentExpenses > 0 ? round($recentExpenses / 2, 2) : 25000;

        $recentIncome = (float) Income::where('user_id', $userId)
            ->where('income_date', '>=', $twoMonthsAgo)
            ->sum('amount');
        $monthlyIncome = $recentIncome > 0 ? round($recentIncome / 2, 2) : 50000;

        $investedAssets = (float) Investment::where('user_id', $userId)->sum('current_value');
        $netWorth = $liquidCash + $investedAssets;

        return view('time-machine.index', [
            'liquidCash'     => $liquidCash,
            'monthlyBurn'    => $monthlyBurn,
            'monthlyIncome'  => $monthlyIncome,
            'investedAssets' => $investedAssets,
            'netWorth'       => $netWorth,
        ]);
    }

    /**
     * Generate an AI crisis defense strategy for the simulated scenario.
     */
    public function generateStrategy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_name'  => 'required|string|max:100',
            'survival_rate'  => 'required|numeric',
            'runway_months'  => 'required|numeric',
            'projected_cash' => 'required|numeric',
        ]);

        $user = auth()->user();
        $scenarioName  = $validated['scenario_name'];
        $survivalRate  = $validated['survival_rate'];
        $runwayMonths  = $validated['runway_months'];
        $projectedCash = $validated['projected_cash'];

        $apiKey = config('services.gemini.api_key');
        $bullets = [];

        if ($apiKey) {
            try {
                $prompt = <<<PROMPT
You are a elite crisis-management financial strategist for FinSight AI.
The user just ran a Monte Carlo crisis simulation with these results:
- Simulated Scenario: {$scenarioName}
- Solvency / Survival Rate: {$survivalRate}%
- Projected Runway: {$runwayMonths} months
- Remaining Net Cash: Rs {$projectedCash}

Provide exactly 3 concise, highly tactical bullet recommendations on how {$user->name} can bulletproof their finances against this scenario.
Format output as a raw JSON array of 3 strings:
["Action 1...", "Action 2...", "Action 3..."]
Keep each bullet under 25 words. All amounts in Rs.
PROMPT;

                $apiResponse = Http::timeout(20)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]],
                        ],
                    ]
                );

                if ($apiResponse->successful()) {
                    $raw = data_get($apiResponse->json(), 'candidates.0.content.parts.0.text', '');
                    $clean = trim(preg_replace('/^```(?:json)?|```$/m', '', $raw));
                    $decoded = json_decode($clean, true);
                    if (is_array($decoded) && count($decoded) >= 2) {
                        $bullets = array_slice($decoded, 0, 3);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('TimeMachineController: Strategy generation error — ' . $e->getMessage());
            }
        }

        if (empty($bullets)) {
            $bullets = [
                "Establish a dedicated 6-month liquid emergency fund in a high-yield liquid mutual fund before triggering this scenario.",
                "Cut non-essential discretionary spends (dining, entertainment, subscriptions) by 30% to extend your cash runway by 4.2 months.",
                "Avoid pledging or liquidating long-term equity assets at a loss during peak market drawdowns; lean on debt buffers first."
            ];
        }

        return response()->json([
            'success'   => true,
            'scenario'  => $scenarioName,
            'survival'  => $survivalRate,
            'runway'    => $runwayMonths,
            'strategy'  => $bullets,
        ]);
    }
}
