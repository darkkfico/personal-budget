<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auto_budgets', function (Blueprint $table) {
            $table->date('reset_carry_answered_on')->nullable()->after('reset_date');
        });

        Schema::table('custom_budgets', function (Blueprint $table) {
            $table->date('reset_carry_answered_on')->nullable()->after('reset_date');
        });
    }

    public function down(): void
    {
        Schema::table('auto_budgets', function (Blueprint $table) {
            $table->dropColumn('reset_carry_answered_on');
        });

        Schema::table('custom_budgets', function (Blueprint $table) {
            $table->dropColumn('reset_carry_answered_on');
        });
    }
};
