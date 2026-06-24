<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('billing_type')->nullable()->after('quantity');
            $table->decimal('start_kms_reading', 12, 2)->nullable()->after('unloading_date');
            $table->string('lr_no')->nullable()->after('start_kms_reading');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['billing_type', 'start_kms_reading', 'lr_no']);
        });
    }
};
