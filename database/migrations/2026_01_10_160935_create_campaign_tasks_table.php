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
        Schema::create('campaign_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('title', 50);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->enum('status', ["planning", "ongoing", "on_hold", "accomplished"])->default('planning');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_tasks');
    }
};
