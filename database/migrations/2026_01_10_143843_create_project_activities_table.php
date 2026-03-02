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
        Schema::create('project_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action_type'); // 'created', 'updated', 'status_changed', 'task_added', 'task_updated', 'task_deleted', 'contributor_added', 'contributor_removed', 'task_status_changed'
            $table->text('description'); // Human-readable description of the action
            $table->json('metadata')->nullable(); // Additional data (old values, new values, etc.)
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_activities');
    }
};
