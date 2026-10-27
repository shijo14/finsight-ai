<?php

namespace App\Services;

use Carbon\Carbon;

class UpiParserService
{
    /**
     * Parse raw bank debit/credit SMS or UPI notification text into structured data.
     *
     * @param string $sms
     * @return array{amount: float, type: string, merchant: string, category: string, account: ?string, balance: ?float, date: string, success: bool}
     */
    public function parse(string $sms): array
    {
        $text = trim($sms);
        $clean = preg_replace('/\s+/', ' ', $text);

        // 1. Detect transaction type (debit vs credit)
        $isCredit = preg_match('/\b(credited|deposited|received|refunded)\b/i', $clean);
        $type = $isCredit ? 'income' : 'expense';

        // 2. Extract Amount
        // Matches: "Rs 450", "Rs. 1,200.50", "INR 3500", "debited by Rs 150.00", "Paid Rs. 40"
        $amount = 0.00;
        if (preg_match('/(?:rs\.?|inr)\s*([\d,]+(?:\.\d{1,2})?)/i', $clean, $m) ||
            preg_match('/(?:debited|credited|paid|spent)\s+(?:by|for|with)?\s*(?:rs\.?|inr)?\s*([\d,]+(?:\.\d{1,2})?)/i', $clean, $m)) {
            $amount = (float) str_replace(',', '', $m[1]);
        }

        // 3. Extract Merchant / Beneficiary
        $merchant = 'General Transaction';
        if (preg_match('/(?:to\s+vpa|to|towards|transfer\s+to|info:\s*upi\*|at)\s+([a-zA-Z0-9_\-\.\s@]{2,30}?)(?:\s+(?:on|via|ref|bal|avl|\(upi|\.))/i', $clean, $m)) {
            $rawMerchant = trim($m[1]);
            // Clean up VPA handles like swiggy@icici or UPI/BLINKIT/123
            $rawMerchant = preg_replace('/^upi[\/\*]/i', '', $rawMerchant);
            $rawMerchant = preg_replace('/@[a-zA-Z0-9]+$/', '', $rawMerchant);
            $rawMerchant = preg_replace('/[\/\*].*$/', '', $rawMerchant);
            $merchant = ucwords(trim($rawMerchant)) ?: 'Merchant';
        } elseif (preg_match('/\b(swiggy|zomato|blinkit|zepto|uber|ola|rapido|amazon|flipkart|myntra|netflix|spotify|starbucks|chai point|mcdonald)\b/i', $clean, $m)) {
            $merchant = ucwords($m[1]);
        }

        // 4. Extract Account Snippet
        $account = null;
        if (preg_match('/(?:a\/c|account|acct)\s*(?:no\.?|ending)?\s*[\*xX]*(\d{3,4})/i', $clean, $m)) {
            $account = 'XX' . $m[1];
        }

        // 5. Extract Available Balance
        $balance = null;
        if (preg_match('/(?:avl\s*bal(?:ance)?|bal(?::|\s))\s*(?:is\s*)?(?:rs\.?|inr)?\s*([\d,]+(?:\.\d{1,2})?)/i', $clean, $m)) {
            $balance = (float) str_replace(',', '', $m[1]);
        }

        // 6. Extract Date
        $date = now()->format('Y-m-d');
        if (preg_match('/\b(\d{1,2})[\/\-\s]([a-zA-Z]{3}|\d{1,2})[\/\-\s](\d{2,4})\b/', $clean, $m)) {
            try {
                $date = Carbon::parse("{$m[1]} {$m[2]} {$m[3]}")->format('Y-m-d');
            } catch (\Throwable $e) {
                $date = now()->format('Y-m-d');
            }
        }

        // 7. Auto Categorize
        $category = $this->determineCategory($merchant, $clean);

        return [
            'amount'   => $amount,
            'type'     => $type,
            'merchant' => $merchant,
            'category' => $category,
            'account'  => $account,
            'balance'  => $balance,
            'date'     => $date,
            'success'  => $amount > 0,
        ];
    }

    protected function determineCategory(string $merchant, string $fullText): string
    {
        $haystack = strtolower($merchant . ' ' . $fullText);

        $rules = [
            'Food & Dining'  => ['swiggy', 'zomato', 'starbucks', 'mcdonald', 'burger', 'pizza', 'domino', 'cafe', 'restaurant', 'dhaba', 'chai', 'tea', 'coffee', 'kfc', 'subway'],
            'Groceries'      => ['blinkit', 'zepto', 'instamart', 'bigbasket', 'dmart', 'supermarket', 'groceries', 'fresh', 'milk', 'vegetable', 'fruit'],
            'Transportation' => ['uber', 'ola', 'rapido', 'metro', 'petrol', 'fuel', 'hpcl', 'bpcl', 'iocl', 'toll', 'fastag', 'parking', 'auto'],
            'Entertainment'  => ['netflix', 'spotify', 'prime', 'hotstar', 'cinema', 'bookmyshow', 'steam', 'playstation', 'pvr', 'inox', 'youtube'],
            'Shopping'       => ['amazon', 'flipkart', 'myntra', 'zara', 'h&m', 'nykaa', 'ajio', 'meesho', 'croma', 'reliance digital'],
            'Healthcare'     => ['apollo', 'pharmeasy', 'hospital', 'clinic', 'medical', 'pharmacy', '1mg', 'practo', 'dentist'],
            'Utilities'      => ['bescom', 'electricity', 'broadband', 'airtel', 'jio', 'vi', 'water', 'gas', 'bill', 'recharge', 'dth'],
            'Housing'        => ['rent', 'society', 'nobroker', 'maintenance'],
            'Software Tools' => ['github', 'openai', 'anthropic', 'digitalocean', 'aws', 'cursor', 'google storage', 'icloud', 'notion'],
        ];

        foreach ($rules as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($haystack, $keyword)) {
                    return $category;
                }
            }
        }

        return 'Shopping';
    }
}
