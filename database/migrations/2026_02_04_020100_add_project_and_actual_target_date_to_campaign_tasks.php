<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campaign_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('campaign_tasks', 'campaign_project_id')) {
                $table->foreignId('campaign_project_id')->nullable()->after('campaign_id')->constrained('campaign_projects')->nullOnDelete();
            }

            if (!Schema::hasColumn('campaign_tasks', 'actual_target_date')) {
                $table->date('actual_target_date')->nullable()->after('target_date');
            }
        });
    }

    public function down()
    {
        Schema::table('campaign_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('campaign_tasks', 'campaign_project_id')) {
                $table->dropForeign(['campaign_project_id']);
                $table->dropColumn('campaign_project_id');
            }

            if (Schema::hasColumn('campaign_tasks', 'actual_target_date')) {
                $table->dropColumn('actual_target_date');
            }
        });
    }
};
