<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auto_budgets', function (Blueprint $table) {
            $table->decimal('pending_reset_leftover', 12, 2)->nullable()->after('reset_carry_answered_on');
            $table->date('reset_leftover_captured_on')->nullable()->after('pending_reset_leftover');
        });

        Schema::table('custom_budgets', function (Blueprint $table) {
            $table->decimal('pending_reset_leftover', 12, 2)->nullable()->after('reset_carry_answered_on');
            $table->date('reset_leftover_captured_on')->nullable()->after('pending_reset_leftover');
        });
    }

    public function down(): void
    {
        Schema::table('auto_budgets', function (Blueprint $table) {
            $table->dropColumn(['pending_reset_leftover', 'reset_leftover_captured_on']);
        });

        Schema::table('custom_budgets', function (Blueprint $table) {
            $table->dropColumn(['pending_reset_leftover', 'reset_leftover_captured_on']);
        });
    }
};
