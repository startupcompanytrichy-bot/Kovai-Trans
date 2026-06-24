<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Financial expense fields
            $table->decimal('diesel_advance', 12, 2)->default(0)->after('advance_amount');
            $table->decimal('driver_bata', 12, 2)->default(0)->after('diesel_advance');
            $table->decimal('toll_charges', 12, 2)->default(0)->after('driver_bata');
            $table->decimal('loading_charges', 12, 2)->default(0)->after('toll_charges');
            $table->decimal('unloading_charges', 12, 2)->default(0)->after('loading_charges');
            $table->decimal('other_expenses', 12, 2)->default(0)->after('unloading_charges');
            $table->text('expense_notes')->nullable()->after('other_expenses');

            // Collection / payment status
            $table->string('payment_status')->default('pending')->after('balance_amount'); // pending, partial, completed
            $table->decimal('collected_amount', 12, 2)->default(0)->after('payment_status');
            $table->date('collection_due_date')->nullable()->after('collected_amount');
            $table->string('payment_mode')->nullable()->after('collection_due_date'); // cash, upi, bank, cheque
            $table->string('upi_details')->nullable()->after('payment_mode');
            $table->string('bank_details')->nullable()->after('upi_details');

            // Trip workflow status (extended)
            $table->string('workflow_status')->default('pending')->after('status');
            // pending, allocated, started, loading, in_transit, reached, unloading, completed, closed

            // Route details
            $table->decimal('distance_km', 10, 2)->nullable()->after('to_location');
            $table->string('load_type')->nullable()->after('material');

            // Dates
            $table->date('expected_delivery_date')->nullable()->after('loading_date');
            $table->date('booking_date')->nullable()->after('trip_date');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'diesel_advance', 'driver_bata', 'toll_charges', 'loading_charges',
                'unloading_charges', 'other_expenses', 'expense_notes',
                'payment_status', 'collected_amount', 'collection_due_date',
                'payment_mode', 'upi_details', 'bank_details',
                'workflow_status', 'distance_km', 'load_type',
                'expected_delivery_date', 'booking_date',
            ]);
        });
    }
};
