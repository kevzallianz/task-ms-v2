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
        if(!Schema::hasColumn('campaign_projects', 'start_date') && !Schema::hasColumn('campaign_projects', 'target_date')) {
            Schema::table('campaign_projects', function (Blueprint $table) {
                $table->date('start_date')->nullable()->after('description');
                $table->date('target_date')->nullable()->after('start_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('campaign_projects', 'start_date') && Schema::hasColumn('campaign_projects', 'target_date')) {
            Schema::table('campaign_projects', function (Blueprint $table) {
                $table->dropColumn(['start_date', 'target_date']);
            });
        }
    }
};
