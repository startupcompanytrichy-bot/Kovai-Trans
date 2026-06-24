<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'document_number')) {
                $table->string('document_number')->nullable()->after('lr_no');
            }
            if (!Schema::hasColumn('trips', 'document_path')) {
                $table->string('document_path')->nullable()->after('document_number');
            }
            if (!Schema::hasColumn('trips', 'booking_date')) {
                $table->date('booking_date')->nullable()->after('trip_no');
            }
            if (!Schema::hasColumn('trips', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable()->after('booking_date');
            }
            if (!Schema::hasColumn('trips', 'distance_km')) {
                $table->decimal('distance_km', 10, 2)->nullable()->after('to_location');
            }
            if (!Schema::hasColumn('trips', 'load_type')) {
                $table->string('load_type')->nullable()->after('material');
            }
            if (!Schema::hasColumn('trips', 'diesel_advance')) {
                $table->decimal('diesel_advance', 12, 2)->default(0)->after('advance_amount');
            }
            if (!Schema::hasColumn('trips', 'driver_bata')) {
                $table->decimal('driver_bata', 12, 2)->default(0)->after('diesel_advance');
            }
            if (!Schema::hasColumn('trips', 'toll_charges')) {
                $table->decimal('toll_charges', 12, 2)->default(0)->after('driver_bata');
            }
            if (!Schema::hasColumn('trips', 'loading_charges')) {
                $table->decimal('loading_charges', 12, 2)->default(0)->after('toll_charges');
            }
            if (!Schema::hasColumn('trips', 'unloading_charges')) {
                $table->decimal('unloading_charges', 12, 2)->default(0)->after('loading_charges');
            }
            if (!Schema::hasColumn('trips', 'other_expenses')) {
                $table->decimal('other_expenses', 12, 2)->default(0)->after('unloading_charges');
            }
            if (!Schema::hasColumn('trips', 'expense_notes')) {
                $table->string('expense_notes')->nullable()->after('other_expenses');
            }
            if (!Schema::hasColumn('trips', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('balance_amount');
            }
            if (!Schema::hasColumn('trips', 'collected_amount')) {
                $table->decimal('collected_amount', 12, 2)->default(0)->after('payment_status');
            }
            if (!Schema::hasColumn('trips', 'collection_due_date')) {
                $table->date('collection_due_date')->nullable()->after('collected_amount');
            }
            if (!Schema::hasColumn('trips', 'payment_mode')) {
                $table->string('payment_mode')->nullable()->after('collection_due_date');
            }
            if (!Schema::hasColumn('trips', 'upi_details')) {
                $table->string('upi_details')->nullable()->after('payment_mode');
            }
            if (!Schema::hasColumn('trips', 'bank_details')) {
                $table->string('bank_details')->nullable()->after('upi_details');
            }
            if (!Schema::hasColumn('trips', 'workflow_status')) {
                $table->string('workflow_status')->default('pending')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['document_number', 'document_path']);
        });
    }
};
