<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('asset_make')->nullable()->after('vehicle_type');  // e.g. EICHER, TATA
            $table->string('asset_type')->nullable()->after('asset_make');    // e.g. PRO 2110
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['asset_make', 'asset_type']);
        });
    }
};
