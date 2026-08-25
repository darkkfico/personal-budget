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
        Schema::create('custom_budget_field_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_budget_snapshot_id')->constrained('custom_budget_snapshots')->cascadeOnDelete();
            $table->string("field_name");
            $table->decimal("field_amount", 10, 2);
            $table->dateTime("snapshot");
            $table->string("month");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_budget_field_snapshots');
    }
};
