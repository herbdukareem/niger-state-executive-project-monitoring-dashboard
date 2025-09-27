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
        Schema::create('output_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('indicator_name');
            $table->text('description');
            $table->string('unit_of_measurement');
            $table->decimal('baseline_value', 15, 2)->default(0);
            $table->decimal('target_value', 15, 2);
            $table->decimal('current_value', 15, 2)->default(0);
            $table->string('data_source');
            $table->string('collection_method');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'annually'])->default('monthly');
            $table->string('responsible_person');
            $table->timestamp('last_updated')->nullable();
            $table->enum('status', ['on_track', 'at_risk', 'off_track', 'achieved'])->default('on_track');
            $table->text('comments')->nullable();
            $table->string('verification_method')->nullable();
            $table->json('assumptions')->nullable();
            $table->json('risks')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('output_indicators');
    }
};
