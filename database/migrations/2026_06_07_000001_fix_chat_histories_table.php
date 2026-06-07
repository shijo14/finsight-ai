<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            if (Schema::hasColumn('chat_histories', 'messages')) {
                $table->renameColumn('messages', 'message');
            }
            if (! Schema::hasColumn('chat_histories', 'response')) {
                $table->text('response')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            if (Schema::hasColumn('chat_histories', 'response')) {
                $table->dropColumn('response');
            }
            if (Schema::hasColumn('chat_histories', 'message')) {
                $table->renameColumn('message', 'messages');
            }
        });
    }
};
