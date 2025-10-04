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
        Schema::create('project_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('update_type', ['progress', 'financial', 'quality', 'site_visit', 'milestone'])->default('progress');
            $table->string('title');
            $table->longText('description');
            $table->decimal('progress_percentage', 5, 2)->nullable();
            $table->json('activities_completed')->nullable();
            $table->json('challenges_faced')->nullable();
            $table->json('next_steps')->nullable();
            $table->decimal('budget_spent_period', 15, 2)->nullable();
            $table->decimal('cumulative_budget_spent', 15, 2)->nullable();
            $table->text('financial_comments')->nullable();
            $table->text('quality_assessment')->nullable();
            $table->text('quality_observations')->nullable();
            $table->json('deliverables_status')->nullable();
            $table->json('stakeholder_feedback')->nullable();
            $table->json('risk_assessment')->nullable();
            $table->json('mitigation_measures')->nullable();
            $table->string('location_visited')->nullable();
            $table->date('visit_date')->nullable();
            $table->string('weather_conditions')->nullable();
            $table->text('site_conditions')->nullable();
            $table->json('safety_observations')->nullable();
            $table->json('recommendations')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['project_id', 'status', 'created_at']);
            $table->index(['created_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_updates');
    }
};
