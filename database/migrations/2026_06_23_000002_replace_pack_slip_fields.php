<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pack_slips', function (Blueprint $table) {
            $table->dropColumn(['folding', 'quantity']);
            $table->string('bale_nos')->nullable()->after('quality');
            $table->integer('no_of_bale')->nullable()->after('bale_nos');
            $table->decimal('total_meter', 12, 2)->nullable()->after('no_of_bale');
        });
    }

    public function down(): void
    {
        Schema::table('pack_slips', function (Blueprint $table) {
            $table->dropColumn(['bale_nos', 'no_of_bale', 'total_meter']);
            $table->string('folding')->nullable()->after('quality');
            $table->decimal('quantity', 12, 2)->nullable()->after('material');
        });
    }
};
