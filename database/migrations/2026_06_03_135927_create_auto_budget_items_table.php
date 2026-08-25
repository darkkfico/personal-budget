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
        Schema::create('auto_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_budget_field_id')->constrained('auto_budget_fields')->cascadeOnDelete();
            $table->string('field_name');
            $table->string('item_name');
            $table->decimal('item_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_budget_items');
    }
};
