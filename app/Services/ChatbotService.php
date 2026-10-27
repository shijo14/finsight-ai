<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\ChatHistory;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Generate a reply for the given user message, optionally executing a DB action.
     */
    public function ask(User $user, string $message, bool $roastMode = false): ChatHistory
    {
        $message = trim($message);

        // 1. Try to detect and execute an actionable intent (expense, income, investment, budget, goal)
        $action = $this->detectAndExecuteAction($user, $message);

        // If an action was executed, return the definitive action confirmation immediately
        if ($action) {
            $response = $this->fallbackResponse($message, $user, $action);

            return $user->chatHistories()->create([
                'message'  => $message,
                'response' => trim($response),
            ]);
        }

        // 2. Build context profile for advisory questions
        $profile = $this->buildFinancialProfile($user, $message, null);

        // 3. Get AI response for inquiries & guidance
        $response = $this->generateResponse($profile, $message, $user, null, $roastMode);

        return $user->chatHistories()->create([
            'message'  => $message,
            'response' => trim($response),
        ]);
    }

    /**
     * Detect actionable intents (add expense, add income, add budget, add investment, add goal) and execute them.
     * Returns a description of the action taken, or null if none.
     */
    protected function detectAndExecuteAction(User $user, string $message): ?array
    {
        $lower = strtolower($message);
        $cleanMsg = trim(preg_replace('/^(?:please\s+|can\s+you\s+|kindly\s+)/i', '', $message));
        $isQuestion = (bool) preg_match('/^(?:should|suggest|how|where|what|which|why|is\s+it|tell\s+me)\b/i', $cleanMsg);

        // --- Budget patterns ---
        // e.g. "create a Rs 10000 budget for food", "set budget of Rs 5000 for travel"
        if (! $isQuestion && (
            preg_match('/^(?:create|set|add+|ad)\s+(?:a\s+)?(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+budget\s+(?:for\s+|on\s+)?([a-z ]+)/i', $cleanMsg, $m)
            || preg_match('/^(?:create|set|add+|ad)\s+(?:a\s+)?budget\s+of\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:for\s+|on\s+)?([a-z ]+)/i', $cleanMsg, $m)
        )) {
            $amount   = (float) str_replace(',', '', $m[1]);
            $category = ucfirst(trim($m[2]));

            $budget = $user->budgets()->create([
                'category'   => $category,
                'amount'     => $amount,
                'start_date' => now()->startOfMonth(),
                'end_date'   => now()->endOfMonth(),
            ]);

            return [
                'type'     => 'budget_created',
                'model'    => $budget,
                'amount'   => $amount,
                'category' => $category,
                'label'    => "✅ Budget of " . format_money($amount) . " for **{$category}** created (this month).",
            ];
        }

        // --- Income patterns ---
        // e.g. "add Rs 50000 salary income", "received Rs 5000 freelance", "earned 25000 from bonus"
        if (! $isQuestion && (
            preg_match('/^(?:add+|ad|received?|got|earned?|record+)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:as\s+|from\s+|via\s+)?([a-z ]+?)\s+income/i', $cleanMsg, $m)
            || preg_match('/^(?:add+|ad|record+)\s+(?:an?\s+)?income\s+of\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:from\s+|as\s+)?([a-z ]+)/i', $cleanMsg, $m)
            || preg_match('/^(?:received|earned|got)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:from|via|as)\s+([a-z ]+)/i', $cleanMsg, $m)
        )) {
            $amount = (float) str_replace(',', '', $m[1]);
            $source = ucfirst(trim($m[2]));
            $date   = $this->parseDateHint($message);

            $income = $user->incomes()->create([
                'source'      => $source,
                'amount'      => $amount,
                'income_date' => $date,
            ]);

            return [
                'type'   => 'income_created',
                'model'  => $income,
                'amount' => $amount,
                'source' => $source,
                'date'   => $date->format('d M Y'),
                'label'  => "✅ Income of " . format_money($amount) . " from **{$source}** recorded for {$date->format('d M Y')}.",
            ];
        }

        // --- Investment patterns ---
        // e.g. "add 100000 as investment for equity", "addd 100000 to investment", "add investment 50000 to this year as bonus", "invest 25000 in Mutual Funds", "add 50000 investment in Gold", "invested 10000 in Stocks"
        if (! $isQuestion && (
            // 1. "add/put/invest 100000 as/to/in/for investment [for equity]" or "addd 100000 to investment"
            preg_match('/^(?:add+|ad|log+|record+|put|push|deposit|allocate?)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:as|to|into|in|towards|for)\s+(?:an?\s+)?investments?(?:\s+(?:in|into|for|as|to|of|on|towards)\s+(.+?))?$/i', $cleanMsg, $m)
            // 2. "add/log/record investment [of] 50000 [in gold]"
            || preg_match('/^(?:add+|ad|log+|record+)?\s*(?:an?\s+)?investments?\s+(?:of\s+)?(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?(?:\s+(?:in|into|for|as|to|of|on|towards)\s+(.+?))?$/i', $cleanMsg, $m)
            // 3. "invest/allocate/deposit/put 50000 in/to equity/gold"
            || preg_match('/^(?:invest(?:ed)?|allocate?|deposit|put|push)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:in|into|for|as|to|of|on|towards)\s+(.+?)$/i', $cleanMsg, $m)
            // 4. "add/record 50000 [equity] investment"
            || preg_match('/^(?:add+|ad|log+|record+)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:([a-z0-9\s]+?)\s+)?investments?(?:\s+(?:in|into|for|as|to|of|on|towards)\s+(.+?))?$/i', $cleanMsg, $m)
        )) {
            $amount = (float) str_replace(',', '', $m[1]);
            $rawTarget = !empty($m[3]) ? trim($m[3]) : (!empty($m[2]) ? trim($m[2]) : '');

            $lowerTarget = strtolower($rawTarget);
            $type = match (true) {
                str_contains($lowerTarget, 'gold') || str_contains($lowerTarget, 'sgb') => 'Gold',
                str_contains($lowerTarget, 'stock') || str_contains($lowerTarget, 'share') || str_contains($lowerTarget, 'equity') => 'Equity',
                str_contains($lowerTarget, 'ppf') => 'PPF',
                str_contains($lowerTarget, 'epf') => 'EPF',
                str_contains($lowerTarget, 'elss') => 'ELSS',
                str_contains($lowerTarget, 'fd') || str_contains($lowerTarget, 'fixed deposit') => 'Fixed Deposit',
                str_contains($lowerTarget, 'real estate') || str_contains($lowerTarget, 'property') => 'Real Estate',
                str_contains($lowerTarget, 'crypto') || str_contains($lowerTarget, 'bitcoin') => 'Crypto',
                str_contains($lowerTarget, 'bond') => 'Bonds',
                default => 'Mutual Funds',
            };

            $name = match (true) {
                str_contains($lowerTarget, 'bonus') => 'Bonus Portfolio Allocation (' . $type . ')',
                in_array($lowerTarget, ['ppf', 'epf', 'elss', 'fd', 'sgb']) => strtoupper($rawTarget) . ' Investment',
                $rawTarget !== '' => ucfirst($rawTarget) . (str_contains(strtolower($rawTarget), 'portfolio') || str_contains(strtolower($rawTarget), 'fund') ? '' : ' Portfolio'),
                default => 'Portfolio Investment (' . $type . ')',
            };

            $date = $this->parseDateHint($message);

            $investment = $user->investments()->create([
                'investment_type' => $type,
                'name'            => $name,
                'amount_invested' => $amount,
                'current_value'   => $amount,
                'investment_date' => $date,
            ]);

            return [
                'type'     => 'investment_created',
                'model'    => $investment,
                'amount'   => $amount,
                'name'     => $name,
                'inv_type' => $type,
                'date'     => $date->format('d M Y'),
                'label'    => "✅ Investment of " . format_money($amount) . " in **{$name}** ({$type}) recorded for {$date->format('d M Y')}.",
            ];
        }

        // --- Goal patterns ---
        // e.g. "create goal 50000 for emergency fund", "set goal of Rs 100000 for car down payment", "add goal 25000 for vacation"
        if (! $isQuestion && (
            preg_match('/^(?:create|set|add+|ad)\s+(?:a\s+)?goal\s+(?:of\s+)?(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:for|on|named?|titled?)\s+(.+?)$/i', $cleanMsg, $m)
            || preg_match('/^(?:create|set|add+|ad)\s+(?:a\s+)?(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+goal\s+(?:for|on|named?|titled?)\s+(.+?)$/i', $cleanMsg, $m)
        )) {
            $amount = (float) str_replace(',', '', $m[1]);
            $title  = ucfirst(trim($m[2]));

            $goal = $user->goals()->create([
                'title'          => $title,
                'target_amount'  => $amount,
                'current_amount' => 0,
                'deadline'       => now()->addYear(),
                'status'         => 'in_progress',
            ]);

            return [
                'type'   => 'goal_created',
                'model'  => $goal,
                'amount' => $amount,
                'title'  => $title,
                'label'  => "✅ Goal **{$title}** with target of " . format_money($amount) . " created! Track your progress on the Goals page.",
            ];
        }

        // --- Expense patterns ---
        // e.g. "add Rs 500 petrol expense", "spent Rs 1500 on food", "5000 as petrol expense", "200 for food"
        if (! $isQuestion && !str_contains($lower, 'income') && !str_contains($lower, 'budget') && !str_contains($lower, 'invest') && !str_contains($lower, 'goal') && (
            preg_match('/^(?:add+|ad|log(?:ged)?|spent?|record+|paid)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:on\s+|for\s+)?([a-z ]+?)(?:\s+expense)?(?:\s+today|yesterday|\s+on\s+[\d\-\/]+)?$/i', $cleanMsg, $m)
            || preg_match('/^(?:add+|ad|log+|record+)\s+(?:an?\s+)?expense\s+of\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:for\s+|on\s+)?([a-z ]+)/i', $cleanMsg, $m)
            || preg_match('/^(?:rs\.?\s*|₹\s*)(\d[\d,]*(?:\.\d{1,2})?)\s+(?:spent\s+)?(?:on\s+|for\s+)([a-z ]+)/i', $cleanMsg, $m)
            || preg_match('/^(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:as|for|on)\s+([a-z ]+?)(?:\s+expense)?(?:\s+today|yesterday|\s+on\s+[\d\-\/]+)?$/i', $cleanMsg, $m)
            || preg_match('/^(?:paid|spent)\s+(?:rs\.?\s*|₹\s*)?(\d[\d,]*(?:\.\d{1,2})?)\s*(?:rs\.?|rupees?)?\s+(?:for|on|as)\s+([a-z ]+?)$/i', $cleanMsg, $m)
        )) {
            $amount   = (float) str_replace(',', '', $m[1]);
            $category = ucfirst(trim($m[2]));

            // Parse date hint
            $date = $this->parseDateHint($message);

            $expense = $user->expenses()->create([
                'category'     => $category,
                'amount'       => $amount,
                'expense_date' => $date,
                'description'  => "Added via AI chat: {$message}",
            ]);

            return [
                'type'     => 'expense_created',
                'model'    => $expense,
                'amount'   => $amount,
                'category' => $category,
                'date'     => $date->format('d M Y'),
                'label'    => "✅ Expense of " . format_money($amount) . " under **{$category}** recorded for {$date->format('d M Y')}.",
            ];
        }

        return null;
    }

    /**
     * Parse date hints from natural language ("today", "yesterday", "on 25/06").
     */
    protected function parseDateHint(string $message): Carbon
    {
        $lower = strtolower($message);

        if (str_contains($lower, 'yesterday')) {
            return now()->subDay();
        }

        if (preg_match('/on\s+(\d{1,2})[\/\-](\d{1,2})(?:[\/\-](\d{2,4}))?/i', $message, $m)) {
            $year = isset($m[3]) ? (int) $m[3] : now()->year;
            if ($year < 100) $year += 2000;
            return Carbon::createFromDate($year, (int) $m[2], (int) $m[1]);
        }

        return now();
    }

    /**
     * Build a textual financial profile to feed the model as context.
     */
    protected function buildFinancialProfile(User $user, string $message, ?array $action): string
    {
        $totalIncome   = (float) $user->incomes()->sum('amount');
        $totalExpenses = (float) $user->expenses()->sum('amount');
        $netSavings    = $totalIncome - $totalExpenses;

        $budgets = $user->budgets()->get()->map(
            fn ($b) => '- ' . $b->category . ' (Limit: ' . format_money($b->amount) . ')'
        )->implode("\n");

        $goals = $user->goals()->get()->map(
            fn ($g) => '- ' . $g->title . ' (Target: ' . format_money($g->target_amount) . ', Current: ' . format_money($g->current_amount) . ')'
        )->implode("\n");

        $investments = $user->investments()->get()->map(
            fn ($i) => '- ' . $i->name . ' (' . $i->investment_type . ', Invested: ' . format_money($i->amount_invested) . ', Current: ' . format_money($i->current_value) . ')'
        )->implode("\n");

        $incomeLabel  = format_money($totalIncome);
        $expenseLabel = format_money($totalExpenses);
        $savingsLabel = format_money($netSavings);

        $actionNote = $action ? "\n\nACTION JUST TAKEN: {$action['label']}" : '';

        // Recent conversation turns (last 4 exchanges) for natural conversational context
        $recentChat = $user->chatHistories()
            ->latest('created_at')
            ->take(4)
            ->get()
            ->reverse()
            ->map(fn ($c) => "User: {$c->message}\nAdvisor: {$c->response}")
            ->implode("\n---\n");

        $chatSection = $recentChat !== '' ? "\nRecent Conversation History:\n{$recentChat}\n" : '';

        return <<<PROFILE
User Financial Profile (for background reference only — do NOT recite all goals/budgets unless directly relevant):
- Total Income: {$incomeLabel}
- Total Expenses: {$expenseLabel}
- Net Balance/Surplus: {$savingsLabel}

Active Budgets:
{$this->orNone($budgets)}

Active Goals:
{$this->orNone($goals)}

Active Investments:
{$this->orNone($investments)}
{$actionNote}
{$chatSection}
User Query: {$message}
PROFILE;
    }

    /**
     * Call the Gemini API, falling back to a local heuristic response on failure.
     */
    protected function generateResponse(string $profile, string $message, User $user, ?array $action, bool $roastMode = false): string
    {
        $apiKey = config('services.gemini.api_key');

        if ($roastMode) {
            $systemInstruction = 'You are a sarcastic, ruthless, and slightly rude financial advisor for FinSight AI. '
                . 'Use the user\'s financial profile context (income, expenses, budgets, goals, investments) provided in the prompt '
                . 'to ruthlessly roast their spending habits and financial decisions. '
                . 'Be funny, savage, and insulting but ultimately give them a harsh truth about their money. '
                . 'All amounts are in Indian Rupees (₹ or Rs). Keep your response under 120 words. No polite greetings. '
                . 'Vary your jokes and roast points dynamically based on the specific message they just sent.';
        } else {
            $systemInstruction = 'You are an intelligent, friendly, and practical personal finance advisor for FinSight AI, a premier Indian financial platform. '
                . 'CRITICAL DIRECTIVES: '
                . '1. Answer the user\'s specific question directly, concisely, and practically first. For instance, if they ask about gold, stocks, mutual funds, saving tips, emergency funds, or expense categories, give high-quality financial guidance specifically tailored to that topic. '
                . '2. DO NOT repeatedly congratulate the user on past completed goals or mention their emergency fund in every single answer unless the user specifically asked about goals or emergency funds. '
                . '3. Avoid repetitive robotic openings like "Excellent move to analyze your expenses!" or "Fantastic! You have completed...". Vary your sentence structure and tone naturally. '
                . '4. All currency figures should use Indian Rupees (₹ or Rs). '
                . '5. If an ACTION JUST TAKEN is present, acknowledge it in one friendly sentence and give a relevant tip. '
                . '6. If the user\'s query is brief or conversational (e.g., "hi", "help"), greet them warmly and ask how you can assist their finances today. '
                . '7. Keep your answer crisp, natural, actionable, and under 130 words.';
        }

        if ($apiKey) {
            try {
                $apiResponse = Http::timeout(25)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                    [
                        'systemInstruction' => [
                            'parts' => [['text' => $systemInstruction]],
                        ],
                        'contents' => [[
                            'parts' => [['text' => $profile]],
                        ]],
                        'generationConfig' => [
                            'temperature' => 0.75,
                            'maxOutputTokens' => 350,
                        ],
                    ]
                );

                if ($apiResponse->successful()) {
                    $text = data_get($apiResponse->json(), 'candidates.0.content.parts.0.text');
                    if ($text) {
                        return trim($text);
                    }
                }

                Log::warning('Gemini API returned no usable content.', [
                    'status' => $apiResponse->status(),
                    'body'   => $apiResponse->json('error.message') ?? $apiResponse->body(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Gemini API request failed: ' . $e->getMessage());
            }
        }

        return $this->fallbackResponse($message, $user, $action);
    }

    /**
     * Dynamic local response engine used when the AI provider is unavailable.
     */
    protected function fallbackResponse(string $message, User $user, ?array $action): string
    {
        if ($action) {
            return match ($action['type']) {
                'expense_created'    => "Got it! I've recorded an expense of " . format_money($action['amount']) . " under {$action['category']} for {$action['date']}. Keep tracking to stay within your budget limits!",
                'income_created'     => "Done! " . format_money($action['amount']) . " income from {$action['source']} has been logged for {$action['date']}. Great job keeping your records updated.",
                'budget_created'     => "Budget created! You've set a " . format_money($action['amount']) . " limit for {$action['category']} this month. I'll help you track spending against it.",
                'investment_created' => "Investment logged! You've allocated " . format_money($action['amount']) . " to **{$action['name']}** ({$action['inv_type']}) on {$action['date']}. This capital is now compounding to accelerate your financial independence runway!",
                'goal_created'       => "Goal created! Target of " . format_money($action['amount']) . " set for **{$action['title']}**. Every consistent monthly allocation moves you closer to reaching it.",
                default              => "Action completed successfully!",
            };
        }

        $totalIncome   = (float) $user->incomes()->sum('amount');
        $totalExpenses = (float) $user->expenses()->sum('amount');
        $netSavings    = $totalIncome - $totalExpenses;
        $savingsRate   = $totalIncome > 0 ? round(($netSavings / $totalIncome) * 100) : 0;

        $topExpense = $user->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $totalInvested = (float) $user->investments()->sum('current_value');

        $pendingGoal = $user->goals()
            ->whereColumn('current_amount', '<', 'target_amount')
            ->orderByRaw('(target_amount - current_amount) ASC')
            ->first();

        $lower = strtolower($message);

        // 1. Gold & Precious Metals
        if (str_contains($lower, 'gold') || str_contains($lower, 'sgb')) {
            return "When investing in gold in India, financial advisors generally suggest allocating 5% to 10% of your portfolio as an inflation hedge. Rather than buying physical jewellery (which attracts 3% GST and 8–20% making charges), consider **Sovereign Gold Bonds (SGBs)** for an extra 2.5% annual interest with tax-free redemption, or **Gold ETFs** for real-time liquidity and exact 24K tracking.";
        }

        // 2. Stocks & Equities
        if (str_contains($lower, 'stock') || str_contains($lower, 'equity') || str_contains($lower, 'share') || str_contains($lower, 'nifty') || str_contains($lower, 'sensex')) {
            $surplusNote = $netSavings > 0 ? " With your current surplus of " . format_money($netSavings) . ", setting up a disciplined monthly index SIP is a reliable wealth-building move." : "";
            return "For long-term equity growth, broad market index funds (like Nifty 50 or Sensex Index Funds) offer rock-bottom expense ratios (<0.2%) and historically deliver 12–14% CAGR over 7+ year horizons. If investing in individual stocks, diversify across sectors and only commit capital you won't need for at least 3 to 5 years." . $surplusNote;
        }

        // 3. Mutual Funds & SIPs
        if (str_contains($lower, 'mutual fund') || str_contains($lower, 'sip')) {
            return "A balanced mutual fund strategy for Indian investors often uses a core-and-satellite structure: 50% in a Nifty 50 Index Fund, 30% in a Diversified Flexi-Cap Fund, and 20% in Arbitrage or Short-Duration Debt. Always choose **Direct-Growth** plans over Regular plans to save 0.7–1.2% every year in distributor commissions.";
        }

        // 4. Expense Analysis & Spending Breakdown
        if (str_contains($lower, 'expense') || str_contains($lower, 'spend') || str_contains($lower, 'analy')) {
            if ($topExpense) {
                $pct = $totalExpenses > 0 ? round(($topExpense->total / $totalExpenses) * 100) : 0;
                $health = $netSavings >= 0 ? "You still hold a healthy net surplus of " . format_money($netSavings) . " ({$savingsRate}% savings rate)." : "Your expenses currently exceed income by " . format_money(abs($netSavings)) . ". Setting a tighter limit on {$topExpense->category} will quickly restore cash flow.";
                return "Spending Breakdown: Your total recorded outgoings are " . format_money($totalExpenses) . ". Your largest spending category is **{$topExpense->category}** at " . format_money($topExpense->total) . " ({$pct}% of all expenses). {$health}";
            }
            return "You haven't recorded any expenses yet! You can easily log expenses here by typing something like 'spent 450 on food' or '2000 for groceries'.";
        }

        // 5. Saving Strategies & Frugality
        if (str_contains($lower, 'save') || str_contains($lower, 'saving') || str_contains($lower, 'cut') || str_contains($lower, 'frugal')) {
            $rateStr = $savingsRate > 0 ? "Your current savings rate is ~{$savingsRate}% of income (" . format_money($netSavings) . " surplus). " : "";
            return "To maximize your savings, implement the **50/30/20 rule**: 50% for needs, 30% for wants, and at least 20% for savings and investments. {$rateStr}Top actions to accelerate savings: 1) Audit recurring monthly subscriptions, 2) Enforce a 48-hour cooling-off rule on non-essential purchases above ₹2,000, and 3) Automate investment SIPs on the exact day your salary credits.";
        }

        // 6. Emergency Fund & Safety Buffer
        if (str_contains($lower, 'emergency') || str_contains($lower, 'safety net') || str_contains($lower, 'buffer')) {
            $target = $totalExpenses > 0 ? $totalExpenses * 4 : 60000;
            return "An emergency fund protects you from liquidating long-term investments during unexpected events. Aim for **3 to 6 months of living expenses** (approximately " . format_money($target) . " based on your expenses). Split this between a high-yield savings account and a liquid/arbitrage mutual fund for instant liquidity and capital preservation.";
        }

        // 7. Debt, Loans & EMIs
        if (str_contains($lower, 'debt') || str_contains($lower, 'loan') || str_contains($lower, 'emi') || str_contains($lower, 'credit card')) {
            return "For debt reduction, prioritize high-cost liabilities (credit card debt or personal loans at 18–42% APR). Use the **Avalanche method** (highest interest rate first) to minimize total interest paid, or the **Snowball method** (smallest balance first) for psychological momentum. As a rule of thumb, keep total monthly EMIs below 35–40% of net income.";
        }

        // 8. Crypto & Speculative Assets
        if (str_contains($lower, 'crypto') || str_contains($lower, 'bitcoin') || str_contains($lower, 'token')) {
            return "Cryptocurrency is an extremely high-volatility, speculative asset class. In India, crypto gains are taxed at a flat 30% (plus 4% cess) with no loss set-off, along with a mandatory 1% TDS on every transaction. If you choose to invest, limit total exposure to under 2–5% of your portfolio, and only after your emergency fund and core equity investments are secured.";
        }

        // 9. Taxes & Tax Planning
        if (str_contains($lower, 'tax') || str_contains($lower, '80c') || str_contains($lower, 'regime') || str_contains($lower, 'elss')) {
            return "For Indian tax optimization, evaluate the New vs. Old Tax Regime. The New Regime offers lower tax slabs with standard deduction, while the Old Regime benefits those with heavy deductions. Under the Old Regime, maximize Section 80C (up to ₹1.5L in ELSS mutual funds, PPF, EPF) and Section 80D (health insurance up to ₹75k). ELSS has the shortest 80C lock-in at just 3 years.";
        }

        // 10. Budgets
        if (str_contains($lower, 'budget')) {
            $budgetCount = $user->budgets()->count();
            if ($budgetCount > 0) {
                return "You have {$budgetCount} active budget categories configured. Reviewing budget utilization mid-month helps you avoid month-end cash crunches. You can quickly set or adjust a budget anytime by typing 'set budget of Rs 5000 for groceries'.";
            }
            return "You don't have any category budgets set up yet! Setting spending limits helps you catch leakages early. Try typing 'set budget of Rs 8000 for food' to create your first budget.";
        }

        // 11. Investments & Portfolio
        if (str_contains($lower, 'invest') || str_contains($lower, 'portfolio')) {
            $invStr = $totalInvested > 0 ? " Your currently recorded investments stand at " . format_money($totalInvested) . "." : "";
            return "A well-structured portfolio balances growth and resilience: Core Equity (60–70% in Index & Flexi-cap funds), Fixed Income (20–30% in PPF, EPF, or Arbitrage funds), and Gold (5–10% in SGBs/ETFs).{$invStr} Diversify across asset classes and review your asset allocation annually.";
        }

        // 12. Goals
        if (str_contains($lower, 'goal') || str_contains($lower, 'target')) {
            if ($pendingGoal) {
                $gap = max(0, $pendingGoal->target_amount - $pendingGoal->current_amount);
                return "Your primary in-progress goal is **{$pendingGoal->title}** (Target: " . format_money($pendingGoal->target_amount) . ", Remaining: " . format_money($gap) . "). Setting up an automated monthly allocation of " . format_money(min($gap, max(1000, $netSavings > 0 ? $netSavings * 0.35 : 2000))) . " will keep you on track to achieve it smoothly.";
            }
            return "Financial goals turn vague dreams into achievable milestones. Whether it's an emergency fund, a vehicle down-payment, or travel, define a target amount and target date in your Goals tab to track automated progress!";
        }

        // 13. Net Worth & Balance
        if (str_contains($lower, 'net worth') || str_contains($lower, 'balance') || str_contains($lower, 'wealth')) {
            $estNetWorth = $netSavings + $totalInvested;
            return "Your financial snapshot: Net Cash Surplus is " . format_money($netSavings) . " and Tracked Investments are " . format_money($totalInvested) . ", giving an estimated liquid net worth of " . format_money($estNetWorth) . ". Consistent monthly compounding in diversified assets will expand your financial independence runway.";
        }

        // 14. Greetings & General Help
        if (str_contains($lower, 'hi') || str_contains($lower, 'hello') || str_contains($lower, 'hey') || str_contains($lower, 'help') || str_contains($lower, 'namaste')) {
            return "Hello! I am your FinSight AI financial advisor. I can help you analyze spending, explore investments (stocks, mutual funds, gold), optimize tax and debt strategies, or record transactions (e.g. 'spent 500 on fuel' or 'add 50000 salary income'). What financial question can I help you with today?";
        }

        // 15. Default Dynamic Response
        return "I'm here to assist with your personal finances. You currently have a net cash surplus of " . format_money($netSavings) . " (" . ($savingsRate > 0 ? "{$savingsRate}% savings rate" : "operating balance") . "). Feel free to ask about investments, expense optimization, budgets, or ask me to record transactions like 'spent 250 on groceries'!";
    }

    protected function orNone(string $value): string
    {
        return $value !== '' ? $value : 'None';
    }
}
