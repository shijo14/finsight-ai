<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Models\ImpulseItem;
use App\Services\ExecutiveSummaryService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $totalIncome    = Income::where('user_id', $userId)->sum('amount');
        $totalExpenses  = Expense::where('user_id', $userId)->sum('amount');
        $budgetCount    = Budget::where('user_id', $userId)->count();
        $goalCount      = Goal::where('user_id', $userId)->count();
        $investmentCount = Investment::where('user_id', $userId)->count();
        $netBalance     = $totalIncome - $totalExpenses;
        $expenseRatio   = $totalIncome > 0 ? round($totalExpenses / $totalIncome * 100, 1) : 0;
        $savingsRate    = $totalIncome > 0 ? round(($netBalance / $totalIncome) * 100, 1) : 0;
        $financialScore = max(0, min(1000, (int) round(700 + $savingsRate * 2.5 + $goalCount * 10 - ($expenseRatio > 80 ? 50 : 0))));

        // Hidden Subscription Hunter
        $hiddenSubscriptions = \Illuminate\Support\Facades\DB::table('expenses')
            ->select('category', 'amount', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->where('user_id', $userId)
            ->where('expense_date', '>=', Carbon::now()->subDays(90))
            ->groupBy('category', 'amount')
            ->having('count', '>=', 2)
            ->get();
            
        // Tax Optimizer (80C Example)
        $total80CInvestments = Investment::where('user_id', $userId)->whereIn('investment_type', ['ELSS', 'PPF', 'EPF'])->sum('amount_invested');
        $taxSavingsNeeded = max(0, 150000 - $total80CInvestments);

        // Cash flow chart: last 6 months actuals + 3 months forecast
        $cashFlowData = $this->buildCashFlowData($userId);

        // 1. Category Spending Breakdown for Donut Chart
        $categorySpendRaw = Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $donutColors = [
            '#06b6d4', // Cyan
            '#10b981', // Emerald
            '#f59e0b', // Amber
            '#ec4899', // Pink
            '#8b5cf6', // Violet
            '#3b82f6', // Blue
            '#f97316', // Orange
            '#14b8a6', // Teal
            '#6366f1', // Indigo
            '#64748b', // Slate
        ];

        $categoryLabels = [];
        $categoryTotals = [];
        $categoryColors = [];
        $categoryPercentages = [];
        $grandExpenseTotal = (float) $categorySpendRaw->sum('total');

        foreach ($categorySpendRaw as $idx => $row) {
            $categoryLabels[] = $row->category;
            $categoryTotals[] = round((float) $row->total, 2);
            $categoryColors[] = $donutColors[$idx % count($donutColors)];
            $categoryPercentages[] = $grandExpenseTotal > 0 ? round(($row->total / $grandExpenseTotal) * 100, 1) : 0;
        }

        $categorySpendData = [
            'labels'      => $categoryLabels,
            'totals'      => $categoryTotals,
            'colors'      => $categoryColors,
            'percentages' => $categoryPercentages,
            'grandTotal'  => $grandExpenseTotal,
        ];

        // 2. Active Budgets with Burn Rate Analysis
        $budgets = Budget::where('user_id', $userId)->get();
        $activeBudgets = $budgets->map(function ($budget) use ($userId) {
            $startDate = $budget->start_date ?? Carbon::now()->startOfMonth();
            $endDate = $budget->end_date ?? Carbon::now()->endOfMonth();

            $spent = (float) Expense::where('user_id', $userId)
                ->where('category', $budget->category)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            $limit = (float) $budget->amount;
            $remaining = max(0, $limit - $spent);
            $percent = $limit > 0 ? round(($spent / $limit) * 100, 1) : 0;

            $now = Carbon::now();
            $daysLeft = $endDate->isFuture() ? $now->diffInDays($endDate) + 1 : 0;
            $dailyAllowance = ($daysLeft > 0 && $remaining > 0) ? round($remaining / $daysLeft, 0) : 0;

            $status = match (true) {
                $spent > $limit => 'danger',
                $percent >= 80  => 'warning',
                default         => 'healthy',
            };

            return [
                'id'              => $budget->id,
                'category'        => $budget->category,
                'limit'           => $limit,
                'spent'           => $spent,
                'remaining'       => $remaining,
                'percent'         => $percent,
                'status'          => $status,
                'days_left'       => $daysLeft,
                'daily_allowance' => $dailyAllowance,
                'notes'           => $budget->notes,
            ];
        });

        // 3. AI Financial Doctor Dynamic Insights
        $insights = [];
        if ($categorySpendRaw->isNotEmpty()) {
            $topCat = $categorySpendRaw->first();
            $topPct = $grandExpenseTotal > 0 ? round(($topCat->total / $grandExpenseTotal) * 100, 0) : 0;
            $insights[] = [
                'icon'    => '🔥',
                'title'   => 'Top Expenditure Area',
                'badge'   => $topCat->category,
                'text'    => "Your highest spending is on {$topCat->category} (" . format_money($topCat->total) . "), accounting for {$topPct}% of all outflows. Trimming 10% saves " . format_money($topCat->total * 0.1) . " monthly.",
                'type'    => 'expense',
            ];
        } else {
            $insights[] = [
                'icon'    => '✨',
                'title'   => 'Fresh Slate',
                'badge'   => 'Ready',
                'text'    => 'Start logging your daily expenses or scan a receipt to unlock real-time spending insights and category breakdowns.',
                'type'    => 'info',
            ];
        }

        if ($savingsRate >= 30) {
            $insights[] = [
                'icon'    => '🚀',
                'title'   => 'Super Saver Velocity',
                'badge'   => "{$savingsRate}% Saved",
                'text'    => "Outstanding financial discipline! You're retaining {$savingsRate}% of income. Consider deploying surplus into index funds or tax-saving ELSS.",
                'type'    => 'success',
            ];
        } elseif ($savingsRate > 0) {
            $insights[] = [
                'icon'    => '⚖️',
                'title'   => 'Moderate Savings Pace',
                'badge'   => "{$savingsRate}% Saved",
                'text'    => "You're saving {$savingsRate}%. Pushing towards the 20-30% benchmark by targeting non-essential subscriptions will accelerate your goals.",
                'type'    => 'warning',
            ];
        } else {
            $insights[] = [
                'icon'    => '⚠️',
                'title'   => 'Deficit Alert',
                'badge'   => 'Cash Flow Deficit',
                'text'    => 'Outflows currently match or exceed tracked inflows. Review recurring expenses and pause discretionary spends to re-establish cash reserves.',
                'type'    => 'danger',
            ];
        }

        $exceededBudget = $activeBudgets->firstWhere('status', 'danger');
        if ($exceededBudget) {
            $insights[] = [
                'icon'    => '🚨',
                'title'   => 'Budget Limit Exceeded',
                'badge'   => $exceededBudget['category'],
                'text'    => "You have exceeded your {$exceededBudget['category']} budget by " . format_money($exceededBudget['spent'] - $exceededBudget['limit']) . ". Reallocate from other categories to balance the month.",
                'type'    => 'danger',
            ];
        } elseif ($taxSavingsNeeded > 0) {
            $insights[] = [
                'icon'    => '🛡️',
                'title'   => 'Section 80C Tax Optimizer',
                'badge'   => 'Tax Shelter',
                'text'    => "You can shelter up to " . format_money($taxSavingsNeeded) . " more in ELSS or PPF to maximize your tax deductions under the Old Tax Regime.",
                'type'    => 'info',
            ];
        } else {
            $insights[] = [
                'icon'    => '🎯',
                'title'   => 'Tax Shelters Maximized',
                'badge'   => '80C Cap Met',
                'text'    => 'You have fully utilized your Rs 1.5L Section 80C tax shelter! Direct additional investments into broad-market equity index funds.',
                'type'    => 'success',
            ];
        }

        $defaultCategories = [
            'Food & Dining', 'Groceries', 'Housing', 'Utilities',
            'Transportation', 'Entertainment', 'Shopping',
            'Healthcare', 'Software Tools', 'Investment', 'Other'
        ];
        $allCategories = collect($defaultCategories)
            ->merge($categoryLabels)
            ->unique()
            ->values()
            ->all();

        $impulseStats = [
            'active_count'     => ImpulseItem::where('user_id', $userId)->where('status', 'locked')->count(),
            'resisted_savings' => (float) ImpulseItem::where('user_id', $userId)->where('status', 'resisted')->sum('amount'),
            'resisted_count'   => ImpulseItem::where('user_id', $userId)->where('status', 'resisted')->count(),
        ];

        return view('dashboard', [
            'totalIncome'          => $totalIncome,
            'totalExpenses'        => $totalExpenses,
            'netBalance'           => $netBalance,
            'budgetCount'          => $budgetCount,
            'goalCount'            => $goalCount,
            'investmentCount'      => $investmentCount,
            'expenseRatio'         => $expenseRatio,
            'savingsRate'          => $savingsRate,
            'financialScore'       => $financialScore,
            'hiddenSubscriptions'  => $hiddenSubscriptions,
            'taxSavingsNeeded'     => $taxSavingsNeeded,
            'cashFlowData'         => $cashFlowData,
            'categorySpendData'    => $categorySpendData,
            'activeBudgets'        => $activeBudgets,
            'insights'             => $insights,
            'allCategories'        => $allCategories,
            'impulseStats'         => $impulseStats,
        ]);
    }

    public function reports(): View
    {
        $userId = auth()->id();
        $user   = auth()->user();

        $totalIncome    = Income::where('user_id', $userId)->sum('amount');
        $totalExpenses  = Expense::where('user_id', $userId)->sum('amount');
        $budgetCount    = Budget::where('user_id', $userId)->count();
        $goalCount      = Goal::where('user_id', $userId)->count();
        $investmentCount = Investment::where('user_id', $userId)->count();
        $netBalance     = $totalIncome - $totalExpenses;
        $expenseRatio   = $totalIncome > 0 ? round($totalExpenses / $totalIncome * 100, 1) : 0;
        $savingsRate    = $totalIncome > 0 ? round(($netBalance / $totalIncome) * 100, 1) : 0;

        // Expense breakdown by category
        $expensesByCategory = Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // AI Executive Summary
        $executiveSummary = app(ExecutiveSummaryService::class)->generate($user);

        // Monthly cash flow for reports
        $cashFlowData = $this->buildCashFlowData($userId);

        return view('reports.index', [
            'totalIncome'        => $totalIncome,
            'totalExpenses'      => $totalExpenses,
            'netBalance'         => $netBalance,
            'budgetCount'        => $budgetCount,
            'goalCount'          => $goalCount,
            'investmentCount'    => $investmentCount,
            'expenseRatio'       => $expenseRatio,
            'savingsRate'        => $savingsRate,
            'expensesByCategory' => $expensesByCategory,
            'executiveSummary'   => $executiveSummary,
            'cashFlowData'       => $cashFlowData,
        ]);
    }

    /**
     * Build monthly cash flow data for the last 6 months + 3-month linear regression forecast.
     */
    protected function buildCashFlowData(int $userId): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->startOfMonth());
        }

        $labels         = [];
        $incomeData     = [];
        $expenseData    = [];
        $netData        = [];

        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end   = $month->copy()->endOfMonth();

            $income   = (float) Income::where('user_id', $userId)
                ->whereBetween('income_date', [$start, $end])
                ->sum('amount');

            $expenses = (float) Expense::where('user_id', $userId)
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $labels[]      = $month->format('M Y');
            $incomeData[]  = round($income, 2);
            $expenseData[] = round($expenses, 2);
            $netData[]     = round($income - $expenses, 2);
        }

        // Simple linear regression forecast for next 3 months
        $forecastLabels  = [];
        $forecastIncome  = [];
        $forecastExpense = [];
        $forecastNet     = [];

        $incomeForecasted  = $this->linearForecast($incomeData, 3);
        $expenseForecasted = $this->linearForecast($expenseData, 3);

        for ($i = 1; $i <= 3; $i++) {
            $forecastLabels[]  = Carbon::now()->addMonths($i)->format('M Y');
            $forecastIncome[]  = round($incomeForecasted[$i - 1], 2);
            $forecastExpense[] = round($expenseForecasted[$i - 1], 2);
            $forecastNet[]     = round($incomeForecasted[$i - 1] - $expenseForecasted[$i - 1], 2);
        }

        return [
            'labels'          => $labels,
            'incomeData'      => $incomeData,
            'expenseData'     => $expenseData,
            'netData'         => $netData,
            'forecastLabels'  => $forecastLabels,
            'forecastIncome'  => $forecastIncome,
            'forecastExpense' => $forecastExpense,
            'forecastNet'     => $forecastNet,
        ];
    }

    /**
     * Simple linear regression to forecast n future values from a series.
     */
    protected function linearForecast(array $data, int $steps): array
    {
        $n = count($data);
        if ($n === 0) {
            return array_fill(0, $steps, 0);
        }

        $sumX  = 0;
        $sumY  = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($data as $i => $y) {
            $x     = $i + 1;
            $sumX  += $x;
            $sumY  += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $denom = ($n * $sumX2 - $sumX * $sumX);
        if ($denom == 0) {
            $slope     = 0;
            $intercept = $sumY / $n;
        } else {
            $slope     = ($n * $sumXY - $sumX * $sumY) / $denom;
            $intercept = ($sumY - $slope * $sumX) / $n;
        }

        $forecasts = [];
        for ($i = 1; $i <= $steps; $i++) {
            $forecasts[] = max(0, $intercept + $slope * ($n + $i));
        }

        return $forecasts;
    }
}
