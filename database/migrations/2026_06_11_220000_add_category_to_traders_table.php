<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traders', function (Blueprint $table) {
            // Stores the expense category this trader belongs to (e.g. 'fuel', 'repair', 'accessories')
            // NULL means the trader is global and appears in all categories
            $table->string('category', 50)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('traders', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
