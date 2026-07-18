<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create whatsapp_histories table
 *
 * Stores every WhatsApp message sent from the system — both automatic
 * (scheduled command) and manual (Daily Check In UI). Acts as a complete
 * audit log that is separate from vehicle_reminder_sends so all message
 * types can eventually be logged here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_histories', function (Blueprint $table) {
            $table->id();

            // Tenant references
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            // What triggered this message
            $table->string('source', 30)->default('manual');
            // manual = sent from UI, scheduled = sent by cron, update_trigger = sent after date update

            // Vehicle this message is about (nullable for other message types)
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');

            // The contact who received it
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('whatsapp_reminder_contacts')->onDelete('set null');

            // Document / expiry info (nullable for non-document messages)
            $table->string('document_type', 60)->nullable();   // e.g. insurance_expiry_date
            $table->string('document_label', 80)->nullable();  // e.g. Insurance
            $table->date('expiry_date')->nullable();
            $table->integer('days_remaining')->nullable();

            // Message details
            $table->string('contact_number', 20);
            $table->text('message');

            // Status
            $table->string('send_status', 20)->default('pending'); // pending | sent | failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();

            // Who triggered it (null = scheduled/automated)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();

            // Indexes for quick queries
            $table->index(['vehicle_id', 'document_type']);
            $table->index(['send_status']);
            $table->index(['contact_number']);
            $table->index(['sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_histories');
    }
};
