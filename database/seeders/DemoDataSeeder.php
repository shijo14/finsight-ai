<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\Investment;
use App\Models\ChatHistory;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // --- INCOMES ---
        $incomes = [
            ['source' => 'Software Engineering Salary',  'amount' => 6500.00, 'income_date' => '2026-06-01'],
            ['source' => 'Freelance Web Development',     'amount' => 1200.00, 'income_date' => '2026-06-05'],
            ['source' => 'Stock Dividends',               'amount' => 340.00,  'income_date' => '2026-06-10'],
            ['source' => 'YouTube Ad Revenue',             'amount' => 480.00,  'income_date' => '2026-06-12'],
            ['source' => 'Software Engineering Salary',  'amount' => 6500.00, 'income_date' => '2026-05-01'],
            ['source' => 'Freelance Web Development',     'amount' => 950.00,  'income_date' => '2026-05-08'],
            ['source' => 'Consulting Fee',                 'amount' => 750.00,  'income_date' => '2026-05-15'],
        ];

        foreach ($incomes as $income) {
            $user->incomes()->create($income);
        }

        // --- EXPENSES ---
        $expenses = [
            ['category' => 'Rent',              'amount' => 1800.00, 'expense_date' => '2026-06-01', 'description' => 'Monthly apartment rent'],
            ['category' => 'Groceries',         'amount' => 420.00,  'expense_date' => '2026-06-03', 'description' => 'Weekly grocery shopping'],
            ['category' => 'Utilities',         'amount' => 185.00,  'expense_date' => '2026-06-05', 'description' => 'Electricity, water, internet'],
            ['category' => 'Transportation',    'amount' => 130.00,  'expense_date' => '2026-06-06', 'description' => 'Gas and public transit'],
            ['category' => 'Dining Out',        'amount' => 210.00,  'expense_date' => '2026-06-08', 'description' => 'Restaurant meals'],
            ['category' => 'Software Tools',    'amount' => 95.00,   'expense_date' => '2026-06-09', 'description' => 'JetBrains, Figma, hosting'],
            ['category' => 'Gym Membership',    'amount' => 55.00,   'expense_date' => '2026-06-10', 'description' => 'Monthly gym subscription'],
            ['category' => 'Healthcare',        'amount' => 120.00,  'expense_date' => '2026-06-11', 'description' => 'Doctor visit and pharmacy'],
            ['category' => 'Entertainment',     'amount' => 75.00,   'expense_date' => '2026-06-13', 'description' => 'Netflix, Spotify, gaming'],
            ['category' => 'Groceries',         'amount' => 380.00,  'expense_date' => '2026-05-04', 'description' => 'Monthly groceries'],
            ['category' => 'Rent',              'amount' => 1800.00, 'expense_date' => '2026-05-01', 'description' => 'Monthly apartment rent'],
            ['category' => 'Shopping',          'amount' => 250.00,  'expense_date' => '2026-05-20', 'description' => 'New headphones and peripherals'],
        ];

        foreach ($expenses as $expense) {
            $user->expenses()->create($expense);
        }

        // --- BUDGETS ---
        $budgets = [
            ['category' => 'Housing',          'amount' => 2000.00, 'start_date' => '2026-06-01', 'end_date' => '2026-06-30', 'notes' => 'Rent and home maintenance'],
            ['category' => 'Food & Dining',    'amount' => 700.00,  'start_date' => '2026-06-01', 'end_date' => '2026-06-30', 'notes' => 'Groceries and restaurants combined'],
            ['category' => 'Transportation',   'amount' => 200.00,  'start_date' => '2026-06-01', 'end_date' => '2026-06-30', 'notes' => 'Gas, parking, and transit'],
            ['category' => 'Entertainment',    'amount' => 150.00,  'start_date' => '2026-06-01', 'end_date' => '2026-06-30', 'notes' => 'Streaming services and hobbies'],
            ['category' => 'Savings',          'amount' => 2000.00, 'start_date' => '2026-06-01', 'end_date' => '2026-06-30', 'notes' => 'Target monthly savings'],
        ];

        foreach ($budgets as $budget) {
            $user->budgets()->create($budget);
        }

        // --- GOALS ---
        $goals = [
            ['title' => 'Emergency Fund',           'target_amount' => 15000.00, 'current_amount' => 8750.00,  'deadline' => '2026-12-31', 'status' => 'in_progress'],
            ['title' => 'Down Payment on Car',       'target_amount' => 10000.00, 'current_amount' => 4200.00,  'deadline' => '2027-06-01', 'status' => 'in_progress'],
            ['title' => 'Vacation to Japan',         'target_amount' => 5000.00,  'current_amount' => 2100.00,  'deadline' => '2027-03-01', 'status' => 'in_progress'],
            ['title' => 'Online Course Certification','target_amount' => 800.00,   'current_amount' => 800.00,   'deadline' => '2026-05-15', 'status' => 'completed'],
        ];

        foreach ($goals as $goal) {
            $user->goals()->create($goal);
        }

        // --- INVESTMENTS ---
        $investments = [
            ['investment_type' => 'Stocks',       'name' => 'Apple Inc. (AAPL)',       'amount_invested' => 3000.00, 'current_value' => 3480.00, 'investment_date' => '2025-09-15'],
            ['investment_type' => 'Stocks',       'name' => 'NVIDIA Corp (NVDA)',      'amount_invested' => 2500.00, 'current_value' => 3120.00, 'investment_date' => '2025-11-01'],
            ['investment_type' => 'ETF',          'name' => 'Vanguard S&P 500 (VOO)',  'amount_invested' => 5000.00, 'current_value' => 5640.00, 'investment_date' => '2025-06-01'],
            ['investment_type' => 'Crypto',       'name' => 'Bitcoin (BTC)',            'amount_invested' => 1500.00, 'current_value' => 1870.00, 'investment_date' => '2026-01-10'],
            ['investment_type' => 'Mutual Fund',  'name' => 'Fidelity Growth Fund',     'amount_invested' => 2000.00, 'current_value' => 2250.00, 'investment_date' => '2025-08-20'],
        ];

        foreach ($investments as $investment) {
            $user->investments()->create($investment);
        }

        // --- CHAT HISTORY (demo conversations) ---
        $chats = [
            ['message' => 'How am I doing financially this month?', 'response' => 'You are doing well! Your total income this month is Rs 8,520 against Rs 3,090 in expenses, giving you a healthy savings rate of about 64%. Your emergency fund is 58% complete. Keep it up!'],
            ['message' => 'Should I invest more in crypto?', 'response' => 'Your Bitcoin position shows a 24.7% return, which is solid. However, crypto currently makes up a small portion of your portfolio. Consider keeping it under 15% of total investments and focus on diversified assets like your VOO ETF which is performing steadily.'],
        ];

        foreach ($chats as $chat) {
            $user->chatHistories()->create($chat);
        }
    }
}
