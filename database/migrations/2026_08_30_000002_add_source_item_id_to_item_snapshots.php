<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auto_budget_item_snapshots', function (Blueprint $table) {
            $table->unsignedBigInteger('auto_budget_item_id')->nullable()->after('auto_budget_field_snapshot_id')->index();
        });

        Schema::table('custom_budget_item_snapshots', function (Blueprint $table) {
            $table->unsignedBigInteger('custom_budget_item_id')->nullable()->after('custom_budget_field_snapshot_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('auto_budget_item_snapshots', function (Blueprint $table) {
            $table->dropColumn('auto_budget_item_id');
        });

        Schema::table('custom_budget_item_snapshots', function (Blueprint $table) {
            $table->dropColumn('custom_budget_item_id');
        });
    }
};
