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
        Schema::create('custom_budget_field_non_resetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_budget_field_id')
                ->constrained(table: 'custom_budget_fields', indexName: 'cbf_non_resetables_field_fk')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_budget_field_non_resetables');
    }
};
