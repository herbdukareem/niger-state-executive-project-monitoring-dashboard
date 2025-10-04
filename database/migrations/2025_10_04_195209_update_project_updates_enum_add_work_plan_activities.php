<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the enum column to include the new value
        DB::statement("ALTER TABLE project_updates MODIFY COLUMN update_type ENUM('progress', 'financial', 'quality', 'site_visit', 'milestone', 'work_plan_activities') DEFAULT 'progress'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE project_updates MODIFY COLUMN update_type ENUM('progress', 'financial', 'quality', 'site_visit', 'milestone') DEFAULT 'progress'");
    }
};
