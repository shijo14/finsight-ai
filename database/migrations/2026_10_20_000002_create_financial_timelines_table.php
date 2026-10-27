<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('income_multiplier', 5, 2)->default(1.00); // e.g. 1.20 = +20% income, 0.00 = career break
            $table->decimal('expense_multiplier', 5, 2)->default(1.00); // e.g. 0.80 = -20% burn
            $table->decimal('lump_sum_inflow', 14, 2)->default(0.00); // windfalls, asset sale
            $table->decimal('lump_sum_outflow', 14, 2)->default(0.00); // house down payment, business seed
            $table->decimal('monthly_investment_rate', 5, 2)->default(25.00); // % of savings directed to investments
            $table->decimal('market_cagr', 5, 2)->default(12.00); // % annual investment growth
            $table->integer('target_horizon_years')->default(15);
            $table->boolean('is_active_preview')->default(false);
            $table->string('status')->default('draft'); // draft, simulated, merged
            $table->timestamp('merged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_timelines');
    }
};
