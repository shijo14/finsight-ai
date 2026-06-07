<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $totalIncome = Income::where('user_id', $userId)->sum('amount');
        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');
        $budgetCount = Budget::where('user_id', $userId)->count();
        $goalCount = Goal::where('user_id', $userId)->count();
        $investmentCount = Investment::where('user_id', $userId)->count();
        $netBalance = $totalIncome - $totalExpenses;
        $expenseRatio = $totalIncome > 0 ? round($totalExpenses / $totalIncome * 100, 1) : 0;
        $savingsRate = $totalIncome > 0 ? round(($netBalance / $totalIncome) * 100, 1) : 0;
        $financialScore = max(0, min(100, (int) round(72 + $savingsRate * 0.25 + $goalCount)));

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netBalance' => $netBalance,
            'budgetCount' => $budgetCount,
            'goalCount' => $goalCount,
            'investmentCount' => $investmentCount,
            'expenseRatio' => $expenseRatio,
            'savingsRate' => $savingsRate,
            'financialScore' => $financialScore,
        ]);
    }

    public function reports(): View
    {
        $userId = auth()->id();

        $totalIncome = Income::where('user_id', $userId)->sum('amount');
        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');
        $budgetCount = Budget::where('user_id', $userId)->count();
        $goalCount = Goal::where('user_id', $userId)->count();
        $investmentCount = Investment::where('user_id', $userId)->count();
        $netBalance = $totalIncome - $totalExpenses;
        $expenseRatio = $totalIncome > 0 ? round($totalExpenses / $totalIncome * 100, 1) : 0;
        $savingsRate = $totalIncome > 0 ? round(($netBalance / $totalIncome) * 100, 1) : 0;

        return view('reports.index', [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netBalance' => $netBalance,
            'budgetCount' => $budgetCount,
            'goalCount' => $goalCount,
            'investmentCount' => $investmentCount,
            'expenseRatio' => $expenseRatio,
            'savingsRate' => $savingsRate,
        ]);
    }
}
