<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE campaign_tasks MODIFY COLUMN status ENUM (
                'planning',
                'for_approval',
                'ongoing',
                'on_hold',
                'accomplished'
            ) NOT NULL DEFAULT 'planning'"
        );
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE campaign_tasks
            MODIFY status ENUM(
                'planning',
                'for_approval',
                'ongoing',
                'on_hold',
                'accomplished'
            ) DEFAULT 'planning'
        ");
    }
};
