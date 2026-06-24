<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'parties',
        'vehicles',
        'vehicle_documents',
        'suppliers',
        'drivers',
        'expense_categories',
        'financial_years',
        'financiers',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tbl) {
            if (!Schema::hasColumn($tbl, 'company_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->unsignedBigInteger('company_id')->nullable()->after('id');
                    $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
                });
            }
            if (!Schema::hasColumn($tbl, 'branch_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('company_id');
                    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tbl) {
            if (Schema::hasColumn($tbl, 'branch_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                });
            }
            if (Schema::hasColumn($tbl, 'company_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
