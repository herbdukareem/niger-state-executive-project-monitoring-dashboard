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
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('project_location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('lga_id')->nullable()->after('longitude');
            $table->string('ward_id')->nullable()->after('lga_id');
            $table->string('address')->nullable()->after('ward_id');
            $table->text('location_description')->nullable()->after('address');
            
            // Add indexes for location-based queries
            $table->index(['latitude', 'longitude']);
            $table->index('lga_id');
            $table->index('ward_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['lga_id']);
            $table->dropIndex(['ward_id']);
            
            $table->dropColumn([
                'latitude',
                'longitude', 
                'lga_id',
                'ward_id',
                'address',
                'location_description'
            ]);
        });
    }
};
