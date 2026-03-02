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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->enum('status', ['planning', 'in_progress', 'completed', 'on_hold'])->default('planning');
            $table->timestamp('completed_at')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
