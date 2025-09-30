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
        Schema::table('work_plan_activities', function (Blueprint $table) {
            // Add only the new fields that don't exist yet
            $table->string('activity_number')->nullable()->after('project_id');
            $table->boolean('is_tracked')->default(true)->after('category');
            $table->boolean('is_completed')->default(false)->after('is_tracked');
            $table->decimal('percentage_complete', 5, 2)->default(0)->after('is_completed');
            $table->text('variance_comments')->nullable()->after('percentage_complete');

            // Modify existing status enum to include new values
            $table->dropColumn('status');
        });

        // Add the updated status enum in a separate statement
        Schema::table('work_plan_activities', function (Blueprint $table) {
            $table->enum('status', ['not_started', 'in_progress', 'on_track', 'delayed', 'completed', 'on_hold', 'cancelled'])
                  ->default('not_started')
                  ->after('actual_end_date');

            // Add index for activity number
            $table->index(['project_id', 'activity_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_plan_activities', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'activity_number']);
            $table->dropColumn([
                'activity_number',
                'is_tracked',
                'is_completed',
                'percentage_complete',
                'variance_comments'
            ]);

            // Restore original status enum
            $table->dropColumn('status');
        });

        Schema::table('work_plan_activities', function (Blueprint $table) {
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'])
                  ->default('not_started')
                  ->after('actual_end_date');
        });
    }
};
