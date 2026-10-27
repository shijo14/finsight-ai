<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExecutiveSummaryService
{
    /**
     * Generate or retrieve a cached AI executive summary for the user's financial month.
     */
    public function generate(User $user): string
    {
        $cacheKey = "executive_summary_{$user->id}_" . now()->format('Y_m_d');

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            return $this->buildSummary($user);
        });
    }

    /**
     * Force-regenerate the summary (bypass cache).
     */
    public function regenerate(User $user): string
    {
        $cacheKey = "executive_summary_{$user->id}_" . now()->format('Y_m_d');
        Cache::forget($cacheKey);
        return $this->generate($user);
    }

    protected function buildSummary(User $user): string
    {
        $totalIncome   = (float) $user->incomes()->sum('amount');
        $totalExpenses = (float) $user->expenses()->sum('amount');
        $netBalance    = $totalIncome - $totalExpenses;
        $savingsRate   = $totalIncome > 0 ? round(($netBalance / $totalIncome) * 100, 1) : 0;
        $budgetCount   = $user->budgets()->count();
        $goalCount     = $user->goals()->count();
        $investCount   = $user->investments()->count();

        // Monthly breakdown (current month)
        $thisMonth = now()->startOfMonth();
        $monthIncome   = (float) $user->incomes()->where('income_date', '>=', $thisMonth)->sum('amount');
        $monthExpenses = (float) $user->expenses()->where('expense_date', '>=', $thisMonth)->sum('amount');

        $topExpenseCategory = $user->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $context = <<<CONTEXT
Financial Summary for {$user->name}:
- Total Income (all time): Rs {$totalIncome}
- Total Expenses (all time): Rs {$totalExpenses}
- Net Balance: Rs {$netBalance}
- Savings Rate: {$savingsRate}%
- This Month Income: Rs {$monthIncome}
- This Month Expenses: Rs {$monthExpenses}
- Active Budgets: {$budgetCount}
- Financial Goals: {$goalCount}
- Investments tracked: {$investCount}
- Top spending category: {$topExpenseCategory?->category} (Rs {$topExpenseCategory?->total})
CONTEXT;

        $apiKey = config('services.gemini.api_key');

        if ($apiKey) {
            try {
                $apiResponse = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'systemInstruction' => [
                            'parts' => [[
                                'text' => 'You are a senior financial analyst writing a concise executive summary for an Indian individual. '
                                    . 'Write exactly 2-3 sentences in a professional, confident tone. '
                                    . 'Mention the savings rate, highlight a strength and one risk area. '
                                    . 'All amounts are in Indian Rupees (Rs). Do not use bullet points.',
                            ]],
                        ],
                        'contents' => [[
                            'parts' => [['text' => $context]],
                        ]],
                    ]
                );

                if ($apiResponse->successful()) {
                    $text = data_get($apiResponse->json(), 'candidates.0.content.parts.0.text');
                    if ($text) {
                        return trim($text);
                    }
                }

                Log::warning('ExecutiveSummaryService: Gemini returned no content.', [
                    'status' => $apiResponse->status(),
                ]);
            } catch (\Throwable $e) {
                Log::error('ExecutiveSummaryService: API error — ' . $e->getMessage());
            }
        }

        // Fallback
        $healthLabel = $savingsRate >= 20 ? 'strong' : ($savingsRate > 0 ? 'moderate' : 'concerning');
        return "Your financial health this period is {$healthLabel}, with a savings rate of {$savingsRate}% on a net balance of "
            . format_money($netBalance) . ". "
            . "You are actively managing {$budgetCount} budget(s) and {$goalCount} financial goal(s), which reflects disciplined planning. "
            . "Continue monitoring your " . ($topExpenseCategory?->category ?? 'top spending') . " category to optimize your cash flow further.";
    }
}
