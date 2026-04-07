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
        if (!Schema::hasColumn('campaign_projects', 'priority')) {
            Schema::table('campaign_projects', function (Blueprint $table) {
                $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('LOW')->after('target_date');
            });
        }
    }

    /**
     * Reverse the migrations.w
     */
    public function down(): void
    {
        //
    }
};
