<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\FinancialTimeline;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MultiverseEngineService
{
    /**
     * Compute current financial baseline for user.
     */
    public function getUserFinancialBaseline(int $userId): array
    {
        $twoMonthsAgo = Carbon::now()->subDays(60);

        $recentIncome = (float) Income::where('user_id', $userId)
            ->where('income_date', '>=', $twoMonthsAgo)
            ->sum('amount');
        $monthlyIncome = $recentIncome > 0 ? round($recentIncome / 2, 2) : 65000;

        $recentExpense = (float) Expense::where('user_id', $userId)
            ->where('expense_date', '>=', $twoMonthsAgo)
            ->sum('amount');
        $monthlyExpense = $recentExpense > 0 ? round($recentExpense / 2, 2) : 38000;

        $totalIncomeAllTime   = (float) Income::where('user_id', $userId)->sum('amount');
        $totalExpensesAllTime = (float) Expense::where('user_id', $userId)->sum('amount');
        $totalInvestments     = (float) Investment::where('user_id', $userId)->sum('amount_invested');

        $liquidBalance = $totalIncomeAllTime - $totalExpensesAllTime;
        $netWorth = max(150000, $liquidBalance + $totalInvestments);

        $savingsRate = $monthlyIncome > 0 ? round((($monthlyIncome - $monthlyExpense) / $monthlyIncome) * 100, 1) : 0;

        return [
            'monthly_income'  => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'net_worth'       => $netWorth,
            'savings_rate'    => $savingsRate,
            'investments'     => $totalInvestments,
            'liquid_cash'     => max(50000, $liquidBalance),
        ];
    }

    /**
     * Seed default iconic multiverse timeline presets if user has none.
     */
    public function ensureDefaultPresets(User $user): Collection
    {
        $existing = $user->timelines()->get();
        if ($existing->isNotEmpty()) {
            return $existing;
        }

        $presets = [
            [
                'name'                    => 'FI/RE Freedom at 42',
                'slug'                    => 'fire-at-42',
                'description'             => 'Aggressive wealth building: +15% side-income, -20% discretionary burn, 45% monthly allocation into Nifty 50 compounding @ 12% CAGR.',
                'income_multiplier'       => 1.15,
                'expense_multiplier'      => 0.80,
                'lump_sum_inflow'         => 0.00,
                'lump_sum_outflow'        => 0.00,
                'monthly_investment_rate' => 45.00,
                'market_cagr'             => 12.00,
                'target_horizon_years'    => 15,
                'is_active_preview'       => true,
                'status'                  => 'simulated',
            ],
            [
                'name'                    => 'The Startup Leap (Founder Risk)',
                'slug'                    => 'startup-leap',
                'description'             => 'Quit corporate life: ₹0 salary for 12 months, -35% frugal survival burn, ₹3 Lakhs seed capital outflow, then asymmetric 18% enterprise upside.',
                'income_multiplier'       => 0.20,
                'expense_multiplier'      => 0.65,
                'lump_sum_inflow'         => 0.00,
                'lump_sum_outflow'        => 300000.00,
                'monthly_investment_rate' => 20.00,
                'market_cagr'             => 15.00,
                'target_horizon_years'    => 10,
                'is_active_preview'       => false,
                'status'                  => 'simulated',
            ],
            [
                'name'                    => 'The Dream Home (Mega Asset)',
                'slug'                    => 'mega-real-estate',
                'description'             => 'Acquire ₹75 Lakh real estate asset: ₹15 Lakhs down payment outflow, +15% monthly expense for EMI/maintenance, +20% career acceleration.',
                'income_multiplier'       => 1.20,
                'expense_multiplier'      => 1.15,
                'lump_sum_inflow'         => 0.00,
                'lump_sum_outflow'        => 1500000.00,
                'monthly_investment_rate' => 20.00,
                'market_cagr'             => 10.00,
                'target_horizon_years'    => 20,
                'is_active_preview'       => false,
                'status'                  => 'simulated',
            ],
        ];

        foreach ($presets as $preset) {
            $user->timelines()->create($preset);
        }

        return $user->timelines()->get();
    }

    /**
     * Compute Git-style diff comparing baseline 'main' vs alternate timeline.
     */
    public function computeDiff(array $mainProj, array $branchProj, array $baseline): array
    {
        $mainYears = $mainProj['years'] ?? [];
        $branchYears = $branchProj['years'] ?? [];

        $horizon = min(count($mainYears), count($branchYears)) - 1;
        $idx5y = min(5, $horizon);
        $idx10y = min(10, $horizon);

        $main5y = $mainYears[$idx5y]['net_worth'] ?? 0;
        $branch5y = $branchYears[$idx5y]['net_worth'] ?? 0;
        $delta5y = $branch5y - $main5y;

        $main10y = $mainYears[$idx10y]['net_worth'] ?? 0;
        $branch10y = $branchYears[$idx10y]['net_worth'] ?? 0;
        $delta10y = $branch10y - $main10y;

        $mainFreedom = $mainProj['freedom_age'] ?? 60;
        $branchFreedom = $branchProj['freedom_age'] ?? 60;
        $freedomDelta = $mainFreedom - $branchFreedom; // positive means years earlier

        $monthlyBurnDelta = ($baseline['monthly_expense'] * (float) ($branchProj['timeline_model']->expense_multiplier ?? 1.0)) - $baseline['monthly_expense'];

        $terminalLines = [
            "diff --git a/reality/main b/reality/" . ($branchProj['slug'] ?? 'branch'),
            "--- a/reality/main",
            "+++ b/reality/" . ($branchProj['slug'] ?? 'branch'),
            "@@ -1,5 +1,5 @@",
            ($delta5y >= 0 ? "+" : "-") . " Net Worth 5y: " . format_money(abs($delta5y)) . ($delta5y >= 0 ? " [SURPLUS]" : " [DRAWDOWN]"),
            ($delta10y >= 0 ? "+" : "-") . " Net Worth 10y: " . format_money(abs($delta10y)) . " (Total: " . format_money($branch10y) . ")",
            ($freedomDelta > 0 ? "+" : "-") . " Freedom Age: " . ($branchFreedom ? "Age {$branchFreedom}" : "Age 65+") . " (" . abs($freedomDelta) . " years " . ($freedomDelta >= 0 ? "earlier" : "delayed") . ")",
            ($monthlyBurnDelta <= 0 ? "+" : "-") . " Monthly Outflow: " . ($monthlyBurnDelta <= 0 ? "-" : "+") . format_money(abs($monthlyBurnDelta)) . "/month discipline shift",
            "+ Compounding Efficiency: " . ($branchProj['timeline_model']->market_cagr ?? 12) . "% CAGR allocated",
        ];

        return [
            'delta_5y'           => $delta5y,
            'delta_10y'          => $delta10y,
            'branch_10y_total'   => $branch10y,
            'freedom_age_main'   => $mainFreedom,
            'freedom_age_branch' => $branchFreedom,
            'freedom_delta'      => $freedomDelta,
            'monthly_burn_delta' => $monthlyBurnDelta,
            'terminal_diff'      => implode("\n", $terminalLines),
        ];
    }

    /**
     * Merge timeline into active reality: updates database Budgets and sets up strategic Goal.
     */
    public function mergeTimelineToReality(User $user, FinancialTimeline $timeline, array $baseline, array $branchProj): array
    {
        $expenseMod = (float) $timeline->expense_multiplier;

        // 1. Adjust all active user budgets by the timeline's expense multiplier
        $updatedBudgetsCount = 0;
        $budgets = $user->budgets()->get();
        if ($budgets->isNotEmpty()) {
            foreach ($budgets as $b) {
                $newLimit = max(1000, round($b->amount * $expenseMod, 2));
                $b->update([
                    'amount' => $newLimit,
                    'notes'  => "Synchronized with Multiverse timeline: '{$timeline->name}'",
                ]);
                $updatedBudgetsCount++;
            }
        } else {
            // Provision baseline budgets tuned to this timeline
            $targetMonthly = $baseline['monthly_expense'] * $expenseMod;
            $user->budgets()->create([
                'category'   => 'Food & Dining',
                'amount'     => round($targetMonthly * 0.35),
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date'   => Carbon::now()->endOfMonth(),
                'notes'      => "Multiverse '{$timeline->name}' auto-budget",
            ]);
            $user->budgets()->create([
                'category'   => 'Shopping & Discretionary',
                'amount'     => round($targetMonthly * 0.25),
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date'   => Carbon::now()->endOfMonth(),
                'notes'      => "Multiverse '{$timeline->name}' auto-budget",
            ]);
            $updatedBudgetsCount += 2;
        }

        // 2. Provision or update target Goal for this timeline vision
        $target5yAmount = $branchProj['years'][min(5, count($branchProj['years']) - 1)]['net_worth'] ?? 2500000;
        $goal = Goal::updateOrCreate(
            [
                'user_id' => $user->id,
                'title'   => "Multiverse Goal: {$timeline->name}",
            ],
            [
                'target_amount'  => max(500000, $target5yAmount),
                'current_amount' => $baseline['net_worth'],
                'deadline'       => Carbon::now()->addYears(5),
                'status'         => 'in_progress',
            ]
        );

        // 3. Mark timeline as merged
        $timeline->update([
            'status'    => 'merged',
            'merged_at' => Carbon::now(),
        ]);

        return [
            'success'               => true,
            'timeline_name'         => $timeline->name,
            'updated_budgets_count' => $updatedBudgetsCount,
            'goal_name'             => $goal->title,
            'target_amount'         => $goal->target_amount,
            'message'               => "Successfully merged '{$timeline->name}'! Adjusted {$updatedBudgetsCount} active budgets and anchored Goal '{$goal->title}' to " . format_money($goal->target_amount) . ".",
        ];
    }

    /**
     * Future-Self AI Dialogue Generator.
     */
    public function generateFutureSelfDialogue(User $user, FinancialTimeline $timeline, string $userMessage, array $diff): string
    {
        $freedomAge = $diff['freedom_age_branch'] ?? 44;
        $delta10y = format_money(abs($diff['delta_10y'] ?? 0));
        $deltaDirection = ($diff['delta_10y'] ?? 0) >= 0 ? "wealthier" : "financially constrained";

        $prompt = "You are the User's 'Future Self' speaking from Year 2038 (Age {$freedomAge}), living in the alternate financial reality called '{$timeline->name}'.
Timeline parameters:
- Income Multiplier: {$timeline->income_multiplier}x
- Expense Multiplier: {$timeline->expense_multiplier}x
- 10-Year Net Worth Divergence vs Main Reality: {$delta10y} {$deltaDirection}
- Financial Freedom reached at: Age {$freedomAge}

The user (your present-day self in 2026) asks:
\"{$userMessage}\"

Instructions:
- Speak in first-person as their authentic Future Self.
- Be deeply empathetic, realistic, and wise. Mention specific emotional and lifestyle trade-offs (what you had to sacrifice, the relief of compounding, or the courage to take the leap).
- Keep it concise, punchy, and conversational (3 to 5 sentences maximum). No robotic bullet points.";

        $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
        if (!empty($apiKey)) {
            try {
                $response = Http::timeout(5)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'role'  => 'user',
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($text)) {
                        return trim($text);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Multiverse: Future-Self Gemini call failed — " . $e->getMessage());
            }
        }

        // High-conviction conversational heuristic fallback
        return "Hey, present me. I'm living in the '{$timeline->name}' timeline right now at age {$freedomAge}. Because you committed to this path back in 2026, we generated an extra {$delta10y} in wealth and unlocked our freedom decades ahead of our peers. Giving up random frivolous purchases felt tight during the first six months, but waking up every morning knowing our passive investments pay for our life was the single best decision we ever made. Merge this branch and stay disciplined.";
    }
}
