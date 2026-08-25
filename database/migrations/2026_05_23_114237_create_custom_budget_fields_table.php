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
        Schema::create('custom_budget_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_budget_id')->constrained('custom_budgets')->cascadeOnDelete();
            $table->string('field_name');
            $table->decimal('field_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_budget_fields');
    }
};
