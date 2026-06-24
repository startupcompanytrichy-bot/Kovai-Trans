<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['trips', 'expenses', 'traders', 'expense_accessories', 'trip_payments', 'expense_payments', 'vehicle_emis', 'emi_payments'];

    public function up(): void
    {
        foreach ($this->tables as $tbl) {
            if (!Schema::hasColumn($tbl, 'fin_year')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->unsignedBigInteger('fin_year')->nullable();
                    $table->foreign('fin_year')->references('id')->on('financial_years')->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tbl) {
            if (Schema::hasColumn($tbl, 'fin_year')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropForeign(['fin_year']);
                    $table->dropColumn('fin_year');
                });
            }
        }
    }
};
