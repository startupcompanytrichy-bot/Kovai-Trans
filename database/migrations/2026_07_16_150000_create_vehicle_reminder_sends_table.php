<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create vehicle_reminder_sends table
 *
 * This table stores details of all WhatsApp reminder messages sent for
 * vehicle document expiries. It acts as an audit log for tracking which
 * reminders were sent, to whom, and when.
 *
 * Used by:
 *   - SendDocumentReminders command (automatic daily reminders)
 *   - DailyCheckInController (manual send from UI)
 *
 * Key features:
 *   - Tracks per-document, per-contact send status
 *   - Prevents duplicate sends via unique constraint checks
 *   - Stores the actual message content sent
 *   - Links to vehicle, contact, and reminder config for reference
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_reminder_sends', function (Blueprint $table) {
            $table->id();

            // Company and branch references (for multi-tenant support)
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            // Vehicle this reminder is for (required, cascades on delete)
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            // WhatsApp contact who received the message (nullable)
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('whatsapp_reminder_contacts')->onDelete('set null');

            // Reminder config that triggered this send (nullable)
            $table->unsignedBigInteger('config_id')->nullable();
            $table->foreign('config_id')->references('id')->on('vehicle_reminder_configs')->onDelete('set null');

            // Document details
            $table->string('document_type');          // insurance_expiry_date, fitness_expiry_date, etc.
            $table->date('expiry_date');               // The document's expiry date
            $table->integer('days_remaining');          // Days until expiry (negative = overdue)

            // Message details
            $table->text('message');                   // The actual WhatsApp message sent
            $table->string('contact_number', 15);      // Phone number the message was sent to

            // Send status tracking
            $table->string('send_status', 20)->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();              // Error details if send failed
            $table->timestamp('sent_at')->nullable();               // Timestamp when message was sent

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();   // User who triggered (null for auto)
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index(['vehicle_id', 'document_type']);  // Quick lookup by vehicle + doc type
            $table->index(['send_status']);                   // Filter by send status
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_reminder_sends');
    }
};
