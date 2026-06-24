<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            // Basic Info
            $table->string('vehicle_name')->nullable();
            $table->string('vehicle_number')->unique();
            $table->string('owner_type')->default('Own');   // Own / Rental
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('vehicle_type')->nullable();     // lorry, truck, trailer, etc.

            // Technical Details
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('rc_number')->nullable();
            $table->string('permit_number')->nullable();

            // Expiry Dates
            $table->date('insurance_expiry_date')->nullable();
            $table->date('fitness_expiry_date')->nullable();
            $table->date('permit_expiry_date')->nullable();
            $table->date('puc_expiry_date')->nullable();

            // Financial
            $table->decimal('opening_balance', 12, 2)->nullable();
            $table->date('opening_balance_date')->nullable();

            // Status & Audit
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
