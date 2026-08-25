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
        Schema::create('custom_budget_item_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_budget_field_snapshot_id')
                ->constrained(table: 'custom_budget_field_snapshots', indexName: 'cbi_snapshots_field_fk')
                ->cascadeOnDelete();
            $table->string('field_name');
            $table->string('item_name');
            $table->decimal('item_amount', 10, 2)->default(0);
            $table->dateTime('snapshot');
            $table->string("month");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_budget_item_snapshots');
    }
};
