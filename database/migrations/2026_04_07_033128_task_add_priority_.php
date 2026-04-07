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
        if (! Schema::hasColumn('campaign_tasks', 'priority')) {
            Schema::table('campaign_tasks', function (Blueprint $table) {
                $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('LOW')->after('target_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('campaign_tasks', 'priority')) {
            Schema::table('campaign_tasks', function (Blueprint $table) {
                $table->dropColumn('priority');
            });
        }
    }
};
