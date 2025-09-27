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
            $table->foreignId('lga_id')->nullable()->constrained('lgas')->after('longitude');
            $table->foreignId('ward_id')->nullable()->constrained('wards')->after('lga_id');
            $table->string('address', 500)->nullable()->after('ward_id');
            $table->text('location_description')->nullable()->after('address');

            // Add indexes for location-based queries
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropForeign(['lga_id']);
            $table->dropForeign(['ward_id']);

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
