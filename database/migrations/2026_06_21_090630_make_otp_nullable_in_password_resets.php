<?php

// ══════════════════════════════════════════════════════════════════
// CONSOLIDATED into 2026_06_21_000001_create_password_resets_table
// (otp is nullable from CREATE — no separate modifier needed)
// ══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: otp is now nullable from the parent CREATE
    }

    public function down(): void
    {
        // No-op
    }
};
