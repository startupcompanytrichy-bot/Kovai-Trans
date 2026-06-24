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
        Schema::create('pack_slips', function (Blueprint $table) {
            $table->id();
            $table->string('lr_no');
            $table->date('slip_date');
            $table->foreignId('pack_customer_id')->constrained()->onDelete('cascade');
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->string('material')->nullable();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('invoice_no')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pack_slips');
    }
};
