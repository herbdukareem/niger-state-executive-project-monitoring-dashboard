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
        Schema::create('work_plan_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('activity_name');
            $table->text('description');
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('not_started');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->string('responsible_person');
            $table->decimal('budget_allocated', 15, 2)->default(0);
            $table->decimal('budget_spent', 15, 2)->default(0);
            $table->json('deliverables')->nullable();
            $table->json('milestones')->nullable();
            $table->json('dependencies')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('category')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['status', 'planned_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_plan_activities');
    }
};
