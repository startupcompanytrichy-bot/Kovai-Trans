<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Add state and district columns for origin
            $table->string('from_state')->nullable()->after('from_location');
            $table->string('from_district')->nullable()->after('from_state');

            // Add state and district columns for destination
            $table->string('to_state')->nullable()->after('to_location');
            $table->string('to_district')->nullable()->after('to_state');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['from_state', 'from_district', 'to_state', 'to_district']);
        });
    }
};
