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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('id_code')->unique();
            $table->foreignId('project_manager_id')->constrained('users');
            $table->string('implementing_organization');
            $table->text('project_location');
            $table->string('sector');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('overall_goal');
            $table->longText('description');
            $table->decimal('total_budget', 15, 2);
            $table->decimal('budget_allocated_current_period', 15, 2)->default(0);
            $table->decimal('cumulative_expenditure', 15, 2)->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'on_hold', 'completed', 'cancelled'])->default('not_started');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->date('monitoring_period_start')->nullable();
            $table->date('monitoring_period_end')->nullable();
            $table->string('monitor_name')->nullable();
            $table->string('monitor_title')->nullable();
            $table->json('data_collection_methods')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['project_manager_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
