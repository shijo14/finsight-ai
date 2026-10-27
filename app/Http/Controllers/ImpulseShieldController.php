<?php

namespace App\Http\Controllers;

use App\Models\ImpulseItem;
use App\Models\Income;
use App\Services\UpiParserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImpulseShieldController extends Controller
{
    /**
     * Display the Impulse Shield vault and life-energy calculator.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Calculate monthly income baseline
        $twoMonthsAgo = Carbon::now()->subDays(60);
        $recentIncome = (float) Income::where('user_id', $user->id)
            ->where('income_date', '>=', $twoMonthsAgo)
            ->sum('amount');
        $monthlyIncome = $recentIncome > 0 ? round($recentIncome / 2, 2) : 50000;

        $coolingItems  = $user->impulseItems()->where('status', 'cooling')->orderBy('cooldown_until')->get();
        $resistedItems = $user->impulseItems()->where('status', 'resisted')->orderByDesc('updated_at')->get();
        $boughtItems   = $user->impulseItems()->where('status', 'bought')->orderByDesc('updated_at')->get();

        $totalResistedMoney = (float) $resistedItems->sum('price');
        $totalCoolingMoney  = (float) $coolingItems->sum('price');

        $hourlyWage = max(100, $monthlyIncome / 176);
        $totalHoursSaved = round($totalResistedMoney / $hourlyWage, 1);

        return view('impulse-shield.index', [
            'coolingItems'       => $coolingItems,
            'resistedItems'      => $resistedItems,
            'boughtItems'        => $boughtItems,
            'totalResistedMoney' => $totalResistedMoney,
            'totalCoolingMoney'  => $totalCoolingMoney,
            'totalHoursSaved'    => $totalHoursSaved,
            'monthlyIncome'      => $monthlyIncome,
        ]);
    }

    /**
     * Lock an impulse purchase into the 48-hour vault.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:150',
            'price'     => 'required|numeric|min:1',
            'category'  => 'nullable|string|max:100',
            'notes'     => 'nullable|string|max:300',
        ]);

        $user = auth()->user();

        $item = $user->impulseItems()->create([
            'item_name'      => $validated['item_name'],
            'price'          => $validated['price'],
            'category'       => !empty($validated['category']) ? $validated['category'] : 'Shopping',
            'cooldown_until' => Carbon::now()->addHours(48),
            'status'         => 'cooling',
            'notes'          => $validated['notes'] ?? null,
        ]);

        return redirect()->route('impulse-shield.index')
            ->with('status', "🛡️ Locked '{$item->item_name}' into Impulse Shield! 48-hour cooldown initiated.");
    }

    /**
     * Mark item as resisted (save the money).
     */
    public function resist(int $id): RedirectResponse
    {
        $item = auth()->user()->impulseItems()->findOrFail($id);
        $item->update(['status' => 'resisted']);

        return redirect()->route('impulse-shield.index')
            ->with('status', "🏆 Outstanding discipline! You resisted buying '{$item->item_name}' and saved " . format_money($item->price) . "!");
    }

    /**
     * Buy item after cooldown and convert to Expense.
     */
    public function buy(int $id): RedirectResponse
    {
        $user = auth()->user();
        $item = $user->impulseItems()->findOrFail($id);
        $item->update(['status' => 'bought']);

        // Log as expense
        $user->expenses()->create([
            'category'     => $item->category,
            'amount'       => $item->price,
            'expense_date' => Carbon::now(),
            'description'  => "Purchased after 48h cooldown: {$item->item_name}",
        ]);

        return redirect()->route('impulse-shield.index')
            ->with('status', "🛒 '{$item->item_name}' marked as purchased and logged to your expenses.");
    }

    /**
     * Parse raw bank SMS or UPI notification.
     */
    public function parseSms(Request $request, UpiParserService $parser): JsonResponse
    {
        $rawSms = $request->input('sms') ?? $request->input('sms_text');

        if (empty($rawSms) || !is_string($rawSms)) {
            return response()->json([
                'success' => false,
                'message' => 'The sms or sms_text field is required.',
            ], 422);
        }

        $result = $parser->parse($rawSms);

        return response()->json($result);
    }
}
