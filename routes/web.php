<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ImpulseShieldController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\JarvisController;
use App\Http\Controllers\MultiverseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuickTransactionController;
use App\Http\Controllers\TimeMachineController;
use App\Http\Controllers\ZenQuantNexusController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('expenses', ExpenseController::class);
    Route::resource('incomes', IncomeController::class);
    Route::resource('goals', GoalController::class);
    Route::resource('investments', InvestmentController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('competitors', CompetitorController::class);


    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports.index');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    Route::post('/quick-transaction', [QuickTransactionController::class, 'store'])->name('quick-transaction.store');
    Route::post('/api/receipt-scan', [QuickTransactionController::class, 'scanReceipt'])->name('receipt.scan');
    Route::post('/demo/seed', [QuickTransactionController::class, 'seedDemo'])->name('demo.seed');

    // Jarvis Voice CFO
    Route::post('/api/jarvis/voice-query', [JarvisController::class, 'query'])->name('jarvis.query');

    // Financial Time Machine (Monte Carlo Life & Disaster Simulator)
    Route::get('/time-machine', [TimeMachineController::class, 'index'])->name('time-machine.index');
    Route::post('/api/time-machine/ai-strategy', [TimeMachineController::class, 'generateStrategy'])->name('time-machine.strategy');

    // Impulse Shield (Anti-Impulse Buying & 48h Cooldown Vault)
    Route::get('/impulse-shield', [ImpulseShieldController::class, 'index'])->name('impulse-shield.index');
    Route::post('/impulse-shield', [ImpulseShieldController::class, 'store'])->name('impulse-shield.store');
    Route::post('/impulse-shield/{id}/resist', [ImpulseShieldController::class, 'resist'])->name('impulse-shield.resist');
    Route::post('/impulse-shield/{id}/buy', [ImpulseShieldController::class, 'buy'])->name('impulse-shield.buy');

    // Instant UPI / Bank SMS Auto-Parser
    Route::post('/api/parse-sms', [ImpulseShieldController::class, 'parseSms'])->name('sms.parse');

    // Financial Multiverse & AI Digital Twin (Git for Your Money)
    Route::get('/multiverse', [MultiverseController::class, 'index'])->name('multiverse.index');
    Route::post('/multiverse/fork', [MultiverseController::class, 'fork'])->name('multiverse.fork');
    Route::post('/multiverse/{id}/merge', [MultiverseController::class, 'merge'])->name('multiverse.merge');
    Route::delete('/multiverse/{id}', [MultiverseController::class, 'destroy'])->name('multiverse.destroy');
    Route::post('/api/multiverse/{id}/future-self-chat', [MultiverseController::class, 'futureSelfChat'])->name('multiverse.future-self-chat');

    // The Zen & Quant Nexus (Asian Syndicate: War Room, Escape Velocity & Kakeibo Radar)
    Route::get('/nexus', [ZenQuantNexusController::class, 'index'])->name('nexus.index');
    Route::post('/api/nexus/war-room', [ZenQuantNexusController::class, 'summonCouncil'])->name('nexus.war-room');
    Route::get('/api/nexus/telemetry', [ZenQuantNexusController::class, 'getLiveTelemetry'])->name('nexus.telemetry');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';