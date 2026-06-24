<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pack_slips', function (Blueprint $table) {
            $table->string('bill_no')->nullable()->after('lr_no');
            $table->string('lot_no')->nullable()->after('bill_no');
            $table->string('quality')->nullable()->after('material');
            $table->string('folding')->nullable()->after('quality');
        });
    }

    public function down(): void
    {
        Schema::table('pack_slips', function (Blueprint $table) {
            $table->dropColumn(['bill_no', 'lot_no', 'quality', 'folding']);
        });
    }
};
