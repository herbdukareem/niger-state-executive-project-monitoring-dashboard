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
        Schema::create('project_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_update_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->enum('type', ['photo', 'document', 'video', 'other'])->default('document');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['project_id', 'type']);
            $table->index(['project_update_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_attachments');
    }
};
