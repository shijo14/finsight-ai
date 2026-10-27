<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\Investment;
use App\Services\ReceiptScannerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuickTransactionController extends Controller
{
    /**
     * Scan an uploaded receipt with Gemini Vision OCR.
     */
    public function scanReceipt(Request $request, ReceiptScannerService $scanner): JsonResponse
    {
        $request->validate([
            'receipt' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
        ]);

        $result = $scanner->scan($request->file('receipt'));

        return response()->json($result);
    }

    /**
     * Store a quick transaction (Expense or Income).
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'type'        => 'required|in:expense,income',
            'amount'      => 'required|numeric|min:0.01',
            'category'    => 'nullable|string|max:100',
            'source'      => 'nullable|string|max:100',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $date = Carbon::parse($validated['date']);

        if ($validated['type'] === 'expense') {
            $category = $validated['category'] ?: 'Other';
            $expense = $user->expenses()->create([
                'category'     => $category,
                'amount'       => $validated['amount'],
                'expense_date' => $date,
                'description'  => $validated['description'] ?? 'Logged via Quick Add',
            ]);

            $message = 'Expense of ' . format_money($expense->amount) . " under {$category} recorded successfully.";
        } else {
            $source = $validated['source'] ?: ($validated['category'] ?: 'Other Income');
            $income = $user->incomes()->create([
                'source'      => $source,
                'amount'      => $validated['amount'],
                'income_date' => $date,
            ]);

            $message = 'Income of ' . format_money($income->amount) . " from {$source} recorded successfully.";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('dashboard')->with('status', $message);
    }

    /**
     * Seed a rich set of realistic demo data for the current user.
     */
    public function seedDemo(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Add Incomes for the last 3 months
        $incomes = [
            ['source' => 'Monthly Salary', 'amount' => 95000, 'offset_days' => 2],
            ['source' => 'Freelance UI Project', 'amount' => 28000, 'offset_days' => 12],
            ['source' => 'Dividend Payout', 'amount' => 4500, 'offset_days' => 20],
            ['source' => 'Monthly Salary', 'amount' => 95000, 'offset_days' => 32],
            ['source' => 'Consulting Advisory', 'amount' => 35000, 'offset_days' => 45],
            ['source' => 'Monthly Salary', 'amount' => 95000, 'offset_days' => 62],
        ];

        foreach ($incomes as $inc) {
            $user->incomes()->create([
                'source'      => $inc['source'],
                'amount'      => $inc['amount'],
                'income_date' => Carbon::now()->subDays($inc['offset_days']),
            ]);
        }

        // Add Expenses across categories
        $expenses = [
            ['category' => 'Housing', 'amount' => 26000, 'desc' => 'Apartment Monthly Rent', 'days' => 3],
            ['category' => 'Food & Dining', 'amount' => 3450, 'desc' => 'Gourmet Dinner with Friends', 'days' => 1],
            ['category' => 'Groceries', 'amount' => 4820, 'desc' => 'Weekly Supermarket Haul', 'days' => 4],
            ['category' => 'Transportation', 'amount' => 2100, 'desc' => 'Fuel & Toll Charges', 'days' => 5],
            ['category' => 'Utilities', 'amount' => 2950, 'desc' => 'Electricity & Fiber Internet', 'days' => 8],
            ['category' => 'Entertainment', 'amount' => 1499, 'desc' => 'Streaming & Cinema Tickets', 'days' => 9],
            ['category' => 'Food & Dining', 'amount' => 1280, 'desc' => 'Office Lunch Deliveries', 'days' => 11],
            ['category' => 'Shopping', 'amount' => 6400, 'desc' => 'New Noise-Cancelling Headphones', 'days' => 14],
            ['category' => 'Healthcare', 'amount' => 1850, 'desc' => 'Vitamins & Health Checkup', 'days' => 18],
            ['category' => 'Software Tools', 'amount' => 2100, 'desc' => 'Cloud & Dev Subscriptions', 'days' => 22],
            ['category' => 'Groceries', 'amount' => 3950, 'desc' => 'Mid-month Grocery Refill', 'days' => 25],
            ['category' => 'Housing', 'amount' => 26000, 'desc' => 'Apartment Monthly Rent', 'days' => 33],
            ['category' => 'Food & Dining', 'amount' => 7800, 'desc' => 'Family Dining Out & Celebrations', 'days' => 38],
            ['category' => 'Transportation', 'amount' => 3200, 'desc' => 'Car Service & Fuel', 'days' => 42],
        ];

        foreach ($expenses as $exp) {
            $user->expenses()->create([
                'category'     => $exp['category'],
                'amount'       => $exp['amount'],
                'description'  => $exp['desc'],
                'expense_date' => Carbon::now()->subDays($exp['days']),
            ]);
        }

        // Add Active Budgets for this month
        $budgets = [
            ['category' => 'Food & Dining', 'amount' => 12000, 'notes' => 'Restaurants, cafes & food deliveries'],
            ['category' => 'Housing', 'amount' => 30000, 'notes' => 'Rent, society maintenance & repairs'],
            ['category' => 'Transportation', 'amount' => 6000, 'notes' => 'Fuel, cabs, metro & tolls'],
            ['category' => 'Entertainment', 'amount' => 5000, 'notes' => 'Movies, OTT subscriptions, games'],
            ['category' => 'Groceries', 'amount' => 10000, 'notes' => 'Fresh produce & household supplies'],
        ];

        foreach ($budgets as $b) {
            $user->budgets()->create([
                'category'   => $b['category'],
                'amount'     => $b['amount'],
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date'   => Carbon::now()->endOfMonth(),
                'notes'      => $b['notes'],
            ]);
        }

        // Add Goals
        $goals = [
            ['title' => 'Emergency Fund (6 Months)', 'target_amount' => 300000, 'current_amount' => 215000, 'target_date' => Carbon::now()->addMonths(6)],
            ['title' => 'Japan Vacation 2027', 'target_amount' => 250000, 'current_amount' => 110000, 'target_date' => Carbon::now()->addMonths(14)],
            ['title' => 'MacBook Pro M3 Max', 'target_amount' => 220000, 'current_amount' => 180000, 'target_date' => Carbon::now()->addMonths(3)],
        ];

        foreach ($goals as $g) {
            $user->goals()->create($g);
        }

        // Add Investments
        $investments = [
            ['name' => 'Nifty 50 Index Fund', 'investment_type' => 'Mutual Fund', 'amount_invested' => 150000, 'current_value' => 184500, 'investment_date' => Carbon::now()->subMonths(18)],
            ['name' => 'Parag Parikh Flexi Cap', 'investment_type' => 'ELSS', 'amount_invested' => 100000, 'current_value' => 122000, 'investment_date' => Carbon::now()->subMonths(10)],
            ['name' => 'Sovereign Gold Bond (SGB 2023)', 'investment_type' => 'Gold', 'amount_invested' => 60000, 'current_value' => 79000, 'investment_date' => Carbon::now()->subMonths(14)],
            ['name' => 'Public Provident Fund (PPF)', 'investment_type' => 'PPF', 'amount_invested' => 50000, 'current_value' => 53550, 'investment_date' => Carbon::now()->subMonths(8)],
        ];

        foreach ($investments as $inv) {
            $user->investments()->create($inv);
        }

        return redirect()->route('dashboard')->with('status', '🚀 Demo portfolio loaded! All charts, burn-rate gauges, and AI insights are now active.');
    }
}
