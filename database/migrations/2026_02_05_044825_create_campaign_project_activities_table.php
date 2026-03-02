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
        Schema::create('campaign_project_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_project_id')->constrained('campaign_projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action_type'); // 'created', 'updated', 'status_changed', 'task_added', 'task_updated', 'task_deleted', 'task_status_changed'
            $table->text('description'); // Human-readable description of the action
            $table->json('metadata')->nullable(); // Additional data (old values, new values, etc.)
            $table->timestamps();

            $table->index(['campaign_project_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_project_activities');
    }
};
