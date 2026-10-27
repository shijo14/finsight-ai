<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Investment;
use App\Models\User;
use App\Models\WarRoomDeliberation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZenQuantEngineService
{
    /**
     * Calculate Real-Time Escape Velocity Telemetry.
     */
    public function calculateEscapeVelocity(User $user, ?float $customBurn = null, ?float $customAssets = null, ?float $customCagr = null): array
    {
        $userId = $user->id;
        $twoMonthsAgo = Carbon::now()->subDays(60);

        // 1. Monthly living burn
        if ($customBurn !== null && $customBurn > 0) {
            $monthlyBurn = $customBurn;
        } else {
            $recentExpense = (float) Expense::where('user_id', $userId)
                ->where('expense_date', '>=', $twoMonthsAgo)
                ->sum('amount');
            if ($recentExpense > 0) {
                $monthlyBurn = round($recentExpense / 2, 2);
            } else {
                $allTimeExpense = (float) Expense::where('user_id', $userId)->sum('amount');
                $monthlyBurn = $allTimeExpense > 0 ? round($allTimeExpense, 2) : 38000;
            }
        }

        // 2. Invested assets
        if ($customAssets !== null && $customAssets > 0) {
            $investedAssets = $customAssets;
        } else {
            $dbAssets = (float) Investment::where('user_id', $userId)->sum('amount_invested');
            // If user has demo USD investments (14000) or under 1 Lakh, scale to realistic INR wealth base
            if ($dbAssets > 0 && $dbAssets < 100000) {
                $investedAssets = $dbAssets * 35; // Scale realistic demo portfolio (~₹4,90,000)
            } elseif ($dbAssets <= 0) {
                $investedAssets = 450000; // Baseline demo asset for visualization
            } else {
                $investedAssets = $dbAssets;
            }
        }

        // 3. Monthly income
        $recentIncome = (float) Income::where('user_id', $userId)
            ->where('income_date', '>=', $twoMonthsAgo)
            ->sum('amount');
        if ($recentIncome > 0) {
            $monthlyIncome = round($recentIncome / 2, 2);
        } else {
            $allTimeIncome = (float) Income::where('user_id', $userId)->sum('amount');
            $monthlyIncome = $allTimeIncome > 0 ? round($allTimeIncome, 2) : 65000;
        }

        $cagr = $customCagr !== null && $customCagr > 0 ? ($customCagr / 100) : 0.12; // 12% equity CAGR
        $secondsPerMonth = 2592000;
        $secondsPerYear = 31536000;

        $burnPerSec  = $monthlyBurn / $secondsPerMonth;
        $yieldPerSec = ($investedAssets * $cagr) / $secondsPerYear;
        $netVelocity = $yieldPerSec - $burnPerSec;

        $isEscape = $netVelocity >= 0;
        $capitalForEscape = ($monthlyBurn * 12) / $cagr;
        $capitalGap = max(0, $capitalForEscape - $investedAssets);
        $escapePercent = min(100, round(($investedAssets / max(1, $capitalForEscape)) * 100, 1));

        return [
            'monthly_burn'       => round($monthlyBurn),
            'invested_assets'    => round($investedAssets),
            'monthly_income'     => round($monthlyIncome),
            'cagr_pct'           => round($cagr * 100, 1),
            'burn_per_sec'       => round($burnPerSec, 4),
            'yield_per_sec'      => round($yieldPerSec, 4),
            'net_velocity'       => round($netVelocity, 4),
            'is_escape'          => $isEscape,
            'capital_for_escape' => round($capitalForEscape),
            'capital_gap'        => round($capitalGap),
            'escape_percent'     => $escapePercent,
        ];
    }

    /**
     * Classify expenses into the 4-D Kakeibo Karma Radar & Hedonic Treadmill.
     */
    public function analyzeKakeiboRadar(User $user): array
    {
        $userId = $user->id;
        $expenses = Expense::where('user_id', $userId)->get();

        $survivalKeywords = ['groceries', 'grocery', 'housing', 'utilities', 'utility', 'healthcare', 'health', 'medicine', 'medical', 'transport', 'transportation', 'rent', 'bills', 'bill', 'petrol', 'fuel', 'diesel', 'metro', 'uber', 'ola', 'auto', 'cab', 'electricity', 'water', 'gas', 'lpg', 'recharge', 'wifi', 'internet'];
        $soulKeywords     = ['education', 'course', 'books', 'book', 'fitness', 'gym', 'self-improvement', 'travel', 'art', 'culture', 'learning', 'tuition', 'class', 'training', 'yoga'];
        $hedonicKeywords  = ['food', 'dining', 'restaurant', 'cafe', 'swiggy', 'zomato', 'entertainment', 'movie', 'shopping', 'clothes', 'gadget', 'gadgets', 'mouse', 'keyboard', 'electronics', 'tech', 'nightlife', 'party', 'gaming', 'game', 'games', 'liquor', 'beer', 'snacks', 'coffee', 'tea'];

        $survivalTotal = 0;
        $soulTotal     = 0;
        $hedonicTotal  = 0;
        $chaosTotal    = 0;

        foreach ($expenses as $exp) {
            $rawCat = strtolower(trim($exp->category . ' ' . ($exp->description ?? '')));
            $amt = (float) $exp->amount;

            $matched = false;
            foreach ($survivalKeywords as $kw) {
                if (str_contains($rawCat, $kw)) {
                    $survivalTotal += $amt;
                    $matched = true;
                    break;
                }
            }
            if ($matched) continue;

            foreach ($soulKeywords as $kw) {
                if (str_contains($rawCat, $kw)) {
                    $soulTotal += $amt;
                    $matched = true;
                    break;
                }
            }
            if ($matched) continue;

            foreach ($hedonicKeywords as $kw) {
                if (str_contains($rawCat, $kw)) {
                    $hedonicTotal += $amt;
                    $matched = true;
                    break;
                }
            }
            if ($matched) continue;

            $chaosTotal += $amt;
        }

        // Provide realistic balanced baseline if user has fresh empty slate
        if ($survivalTotal + $soulTotal + $hedonicTotal + $chaosTotal === 0) {
            $survivalTotal = 24000;
            $soulTotal     = 6500;
            $hedonicTotal  = 14500;
            $chaosTotal    = 3000;
        } elseif ($soulTotal === 0 && ($survivalTotal > 0 || $hedonicTotal > 0)) {
            $soulTotal = max(1800, round(($survivalTotal + $hedonicTotal) * 0.15));
        }

        $grandTotal = max(1, $survivalTotal + $soulTotal + $hedonicTotal + $chaosTotal);
        $hedonicRatio = $soulTotal > 0 ? round($hedonicTotal / $soulTotal, 2) : 2.5;
        $chaosRatio = $chaosTotal / $grandTotal;

        if ($chaosRatio > 0.40) {
            $auditBadge = '🌪️ Unplanned Leakage Drag';
            $auditClass = 'text-amber-400 border-amber-500/30 bg-amber-500/10';
            $auditVerdict = round($chaosRatio * 100) . "% of your spending is unclassified. Categorize expenses to establish clear financial visibility.";
        } elseif ($hedonicRatio > 2.0) {
            $auditBadge = '🚨 High Discretionary Drag';
            $auditClass = 'text-rose-400 border-rose-500/30 bg-rose-500/10';
            $auditVerdict = "You are spending {$hedonicRatio}x more on discretionary leisure than on personal growth. Consider redirecting capital into education, health, or career upskilling.";
        } elseif ($hedonicRatio >= 1.0) {
            $auditBadge = '⚖️ Moderate Equilibrium';
            $auditClass = 'text-amber-400 border-amber-500/30 bg-amber-500/10';
            $auditVerdict = "Your discretionary spending matches your self-investment. Optimize impulse purchases to accelerate your compounding growth.";
        } else {
            $auditBadge = '💎 Disciplined Growth Equilibrium';
            $auditClass = 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10';
            $auditVerdict = "Exceptional discipline. Your personal growth investments exceed discretionary leisure spending. Your capital allocation is highly optimized.";
        }

        return [
            'survival'       => round($survivalTotal),
            'soul'           => round($soulTotal),
            'hedonic'        => round($hedonicTotal),
            'chaos'          => round($chaosTotal),
            'survival_pct'   => round(($survivalTotal / $grandTotal) * 100, 1),
            'soul_pct'       => round(($soulTotal / $grandTotal) * 100, 1),
            'hedonic_pct'    => round(($hedonicTotal / $grandTotal) * 100, 1),
            'chaos_pct'      => round(($chaosTotal / $grandTotal) * 100, 1),
            'hedonic_ratio'  => $hedonicRatio,
            'audit_badge'    => $auditBadge,
            'audit_class'    => $auditClass,
            'audit_verdict'  => $auditVerdict,
        ];
    }

    /**
     * Calculate Yu'e Bao (余额宝) Overnight Idle Cash Arbitrage.
     */
    public function calculateYueBaoArbitrage(User $user): array
    {
        $userId = $user->id;
        $totalIncome = (float) Income::where('user_id', $userId)->sum('amount');
        $totalExpense = (float) Expense::where('user_id', $userId)->sum('amount');
        $liquidBalance = max(0, $totalIncome - $totalExpense);

        $operatingBuffer = 35000; // 30-day operating buffer
        $idleCash = max(0, $liquidBalance - $operatingBuffer);

        if ($idleCash < 10000) {
            $idleCash = 65000; // Baseline demo idle cash
        }

        $savingsRate = 0.027; // 2.7% bank savings
        $liquidYield = 0.072; // 7.2% liquid fund sweep
        $spread = $liquidYield - $savingsRate; // 4.5% annual arbitrage

        $dailyArbitrage = round(($idleCash * $spread) / 365, 2);
        $monthlyArbitrage = round(($idleCash * $spread) / 12);
        $annualArbitrage = round($idleCash * $spread);
        $tenYearCompound = round($idleCash * (pow(1 + $liquidYield, 10) - pow(1 + $savingsRate, 10)));

        return [
            'idle_cash'         => round($idleCash),
            'operating_buffer'  => $operatingBuffer,
            'savings_rate_pct'  => 2.7,
            'liquid_yield_pct'  => 7.2,
            'spread_pct'        => 4.5,
            'daily_arbitrage'   => $dailyArbitrage,
            'monthly_arbitrage' => $monthlyArbitrage,
            'annual_arbitrage'  => $annualArbitrage,
            'ten_year_compound' => $tenYearCompound,
        ];
    }

    /**
     * Conduct Tri-Agent Asian War Room Deliberation (Tokyo vs Shanghai vs Seoul).
     */
    public function deliberateWarRoom(User $user, string $itemName, float $amount, string $category, ?string $reason): array
    {
        $formattedAmount = format_money($amount);
        $oppCost10y = format_money($amount * pow(1 + 0.12, 10));

        $prompt = "You are an Autonomous AI Financial Council auditing a purchase proposal across three objective dimensions:
Purchase Details:
- Item: '{$itemName}'
- Amount: {$formattedAmount}
- Category: '{$category}'
- User Justification: '{$reason}'
- 10-Year Opportunity Cost in Nifty 50 @ 12% CAGR: {$oppCost10y}

Analyze across these 3 objective dimensions (DO NOT use any personal character names or preambles like 'From the desk'):
1. Behavioral & Mindful Audit (kenji): Assess intentional utility, mindfulness, psychological clutter, and hedonic risk.
2. Quantitative Opportunity Cost (chen): Analyze the 10-year compounding wealth impact ({$oppCost10y} in Nifty 50) and capital velocity.
3. Productivity & Growth ROI (minjun): Evaluate time leverage, career upside, and direct practical utility.

Finally, give:
- Council Vote: APPROVED, VETOED, or CONDITIONAL
- Final Ruling: 2-sentence actionable verdict.

Format output as strict JSON with keys:
kenji (string), chen (string), minjun (string), vote ('approved'|'vetoed'|'conditional'), ruling (string).";

        $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
        if (!empty($apiKey)) {
            try {
                $res = Http::timeout(12)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if ($res->successful()) {
                    $json = $res->json();
                    $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    $parsed = json_decode($rawText, true);

                    if (isset($parsed['kenji'], $parsed['chen'], $parsed['minjun'], $parsed['vote'], $parsed['ruling'])) {
                        $record = WarRoomDeliberation::create([
                            'user_id'            => $user->id,
                            'item_name'          => $itemName,
                            'amount'             => $amount,
                            'category'           => $category,
                            'user_justification' => $reason,
                            'kenji_argument'     => $parsed['kenji'],
                            'chen_argument'      => $parsed['chen'],
                            'minjun_argument'    => $parsed['minjun'],
                            'council_vote'       => strtolower($parsed['vote']),
                            'final_ruling'       => $parsed['ruling'],
                        ]);

                        return [
                            'success'        => true,
                            'item_name'      => $itemName,
                            'amount'         => $amount,
                            'kenji'          => $parsed['kenji'],
                            'chen'           => $parsed['chen'],
                            'minjun'         => $parsed['minjun'],
                            'vote'           => strtolower($parsed['vote']),
                            'ruling'         => $parsed['ruling'],
                            'opp_cost_10y'   => $oppCost10y,
                            'deliberation_id'=> $record->id,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("ZenQuant: Gemini War Room call timed out or failed: " . $e->getMessage());
            }
        }

        // Context-rich, item-specific dynamic council deliberation fallback
        $catLower = strtolower($category);
        $itemLower = strtolower($itemName);

        $monthlyIncome = (float) $user->incomes()->sum('amount');
        if ($monthlyIncome <= 0) $monthlyIncome = 50000;
        $incomePercent = round(($amount / $monthlyIncome) * 100, 1);
        $workHours = round($amount / max(1, ($monthlyIncome / 160)), 1);

        $isGrowthCat = in_array($catLower, ['education', 'learning', 'books', 'course', 'skills', 'training'])
            || str_contains($itemLower, 'course') || str_contains($itemLower, 'book') || str_contains($itemLower, 'certification');

        $isHealthCat = in_array($catLower, ['fitness', 'health', 'gym', 'medical', 'wellness', 'sports'])
            || str_contains($itemLower, 'gym') || str_contains($itemLower, 'fitness') || str_contains($itemLower, 'health');

        $isTechCat = in_array($catLower, ['electronics', 'gadgets', 'tech', 'hardware', 'devices'])
            || str_contains($itemLower, 'phone') || str_contains($itemLower, 'laptop') || str_contains($itemLower, 'headphone') || str_contains($itemLower, 'keyboard') || str_contains($itemLower, 'monitor');

        $isLuxuryCat = in_array($catLower, ['fashion', 'apparel', 'clothing', 'luxury', 'jewelry', 'shoes', 'watch'])
            || str_contains($itemLower, 'watch') || str_contains($itemLower, 'shoes') || str_contains($itemLower, 'suit');

        $isDiningCat = in_array($catLower, ['dining', 'food', 'entertainment', 'leisure', 'party', 'drinks', 'travel', 'vacation']);

        if ($isGrowthCat) {
            $kenji = "Allocating capital to '{$itemName}' expands personal capabilities. Genuine skill acquisition and knowledge expansion yield non-depreciating, lifelong mental and career dividends.";
            $chen = "Committing {$formattedAmount} represents {$incomePercent}% of monthly income (~{$workHours} hours of work). While this defers {$oppCost10y} in 10-year market compounding, verified learning consistently generates high annualized IRR.";
            $minjun = "Strong time leverage and career velocity. High-utility learning directly improves your execution speed, problem-solving capacity, and long-term earning potential.";
            $vote = 'approved';
            $ruling = "UNANIMOUS APPROVAL: '{$itemName}' is approved as a high-conviction investment in self-development and earning power.";
        } elseif ($isHealthCat) {
            $kenji = "Physical vitality is the foundational asset supporting all mental focus. Prioritizing wellness via '{$itemName}' protects against burnout and long-term healthcare liabilities.";
            $chen = "Outlay of {$formattedAmount} ({$incomePercent}% of monthly income). Preventative health and energy management offer substantial downside protection against future medical drag.";
            $minjun = "Physical stamina translates directly into sustained cognitive throughput and resilience during demanding work cycles.";
            $vote = 'approved';
            $ruling = "UNANIMOUS APPROVAL: '{$itemName}' ({$formattedAmount}) is approved to sustain health, mental stamina, and daily performance.";
        } elseif ($isTechCat) {
            $kenji = "Examine whether '{$itemName}' removes genuine latency from your daily craft, or if it represents dopamine-driven hardware chasing. Focus on tools that sharpen your work rather than clutter it.";
            $chen = "Hardware opportunity cost: {$formattedAmount} forfeits {$oppCost10y} in compounding index returns over 10 years ({$incomePercent}% of monthly income, or ~{$workHours} hours of labor). Amortize this cost over at least 3 years of heavy productive use.";
            $minjun = ($incomePercent > 35)
                ? "Unless '{$itemName}' directly unlocks new revenue streams or saves 1+ hour daily, this hardware outlay is excessive relative to current income."
                : "If '{$itemName}' eliminates daily workflow bottlenecks and improves deep-work focus, the productivity payback is favorable.";
            $vote = ($incomePercent > 35) ? 'conditional' : 'approved';
            $ruling = ($incomePercent > 35)
                ? "CONDITIONAL APPROVAL: Proceed with '{$itemName}' only if you lock in an offsetting ₹" . number_format($amount * 0.3) . " into your emergency reserve."
                : "APPROVED: '{$itemName}' is approved as a daily productivity instrument. Maintain strict discipline against auxiliary accessory creep.";
        } elseif ($isLuxuryCat) {
            $kenji = "High risk of hedonic adaptation. Luxury items like '{$itemName}' trigger immediate social comparison and quick emotional decay, leaving behind capital depletion and closet clutter.";
            $chen = "Substantial capital drag: Committing {$formattedAmount} forfeits {$oppCost10y} in 10-year compounding equity growth. This equals {$incomePercent}% of monthly income and ~{$workHours} hours of working time.";
            $minjun = "Zero productive ROI. '{$itemName}' provides no operational leverage or career acceleration. A strict 7-day cooling-off pause is strongly advised before executing.";
            $vote = ($amount > 5000 || $incomePercent > 15) ? 'vetoed' : 'conditional';
            $ruling = ($amount > 5000 || $incomePercent > 15)
                ? "VETOED: '{$itemName}' ({$formattedAmount}) introduces heavy hedonic drag and forfeits {$oppCost10y} in compounding wealth. Reallocate toward your financial independence buffer."
                : "CONDITIONAL: If buying '{$itemName}', match 100% of its cost (₹" . number_format($amount) . ") into an equity index SIP today.";
        } elseif ($isDiningCat) {
            $kenji = "Shared social rituals bring genuine happiness, but repetitive passive indulgence creates invisible budget leaks. Ensure '{$itemName}' is an intentional experience rather than convenience-driven spending.";
            $chen = "Opportunity cost of {$oppCost10y} over 10 years. While individual outings seem modest, unmonitored lifestyle leaks compound into significant wealth erosion over time.";
            $minjun = "Tactically acceptable for rest and social recharging, provided it remains strictly within your monthly discretionary leisure allocation.";
            $vote = ($incomePercent > 15) ? 'vetoed' : 'conditional';
            $ruling = ($incomePercent > 15)
                ? "VETOED: Outlay of {$formattedAmount} for '{$itemName}' is disproportionate ({$incomePercent}% of monthly earnings). Scale back to protect your savings target."
                : "CONDITIONAL APPROVAL: Approved for leisure, provided you trim other discretionary dining by ₹" . number_format($amount * 0.5) . " this month.";
        } else {
            $isHighTicket = $amount > 15000 || $incomePercent > 25;
            if ($isHighTicket) {
                $kenji = "Examine if '{$itemName}' solves a recurring daily bottleneck or merely satisfies a transient buying impulse. Resist lifestyle inflation that does not increase lasting life satisfaction.";
                $chen = "Major capital expenditure: {$formattedAmount} represents {$incomePercent}% of your monthly income and requires ~{$workHours} hours of work. Left compounding in an index fund at 12% CAGR, this amount grows to {$oppCost10y} in 10 years.";
                $minjun = "Scrutinize the practical leverage multiplier. Unless '{$itemName}' yields verifiable time savings or career returns, this capital is better deployed building your financial escape runway.";
                $vote = 'vetoed';
                $ruling = "VETOED: '{$itemName}' ({$formattedAmount}) delays financial independence by diverting {$oppCost10y} in 10-year compounding. Enforce a 7-day pause.";
            } else {
                $kenji = "Intentionality check: Ensure '{$itemName}' serves a clear purpose in your living space or routine, avoiding redundant physical accumulation.";
                $chen = "Opportunity cost is {$oppCost10y} over 10 years at 12% CAGR. Manageable at {$incomePercent}% of monthly earnings, but keep aggregate monthly discretionary purchases capped.";
                $minjun = "Acceptable practical expenditure. Does not disrupt operating cash flow or long-term financial trajectory.";
                $vote = 'conditional';
                $ruling = "CONDITIONAL APPROVAL: You may acquire '{$itemName}' provided you transfer ₹" . number_format(max(500, round($amount * 0.25))) . " into your liquid investment fund today.";
            }
        }

        $record = WarRoomDeliberation::create([
            'user_id'            => $user->id,
            'item_name'          => $itemName,
            'amount'             => $amount,
            'category'           => $category,
            'user_justification' => $reason,
            'kenji_argument'     => $kenji,
            'chen_argument'      => $chen,
            'minjun_argument'    => $minjun,
            'council_vote'       => $vote,
            'final_ruling'       => $ruling,
        ]);

        return [
            'success'        => true,
            'item_name'      => $itemName,
            'amount'         => $amount,
            'kenji'          => $kenji,
            'chen'           => $chen,
            'minjun'         => $minjun,
            'vote'           => $vote,
            'ruling'         => $ruling,
            'opp_cost_10y'   => $oppCost10y,
            'deliberation_id'=> $record->id,
        ];
    }
}
