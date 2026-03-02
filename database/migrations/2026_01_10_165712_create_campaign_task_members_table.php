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
        Schema::create('campaign_task_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_task_id')->constrained('campaign_tasks')->cascadeOnDelete();
            $table->foreignId('campaign_member_id')->constrained('campaign_members')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_task_members');
    }
};
