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
        Schema::create('war_room_deliberations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->decimal('amount', 12, 2);
            $table->string('category')->default('Discretionary');
            $table->text('user_justification')->nullable();
            $table->text('kenji_argument');    // Tokyo Zen Master
            $table->text('chen_argument');     // Shanghai Quant Director
            $table->text('minjun_argument');   // Seoul Growth Strategist
            $table->string('council_vote');    // approved, vetoed, conditional
            $table->text('final_ruling');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('war_room_deliberations');
    }
};
