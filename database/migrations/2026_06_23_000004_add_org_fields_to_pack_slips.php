<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pack_slips', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('company_id');
            $table->unsignedBigInteger('fin_year')->nullable()->after('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('pack_slips', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'branch_id', 'fin_year']);
        });
    }
};
