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
        // Add indexes to projects table for faster lookups
        Schema::table('projects', function (Blueprint $table) {
            $table->index('campaign_id');
            $table->index('status');
        });

        // Add indexes to project_tasks table
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('assigned_campaign_id');
            $table->index('status');
        });

        // Add indexes to project_contributors table
        Schema::table('project_contributors', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('campaign_id');
        });

        // Add indexes to project_remarks table
        Schema::table('project_remarks', function (Blueprint $table) {
            $table->index('project_task_id');
            $table->index('user_id');
        });

        // Add indexes to campaign_members table
        Schema::table('campaign_members', function (Blueprint $table) {
            $table->index('campaign_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['campaign_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['assigned_campaign_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('project_contributors', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['campaign_id']);
        });

        Schema::table('project_remarks', function (Blueprint $table) {
            $table->dropIndex(['project_task_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('campaign_members', function (Blueprint $table) {
            $table->dropIndex(['campaign_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
