<?php

// ══════════════════════════════════════════════════════════════════
// CONSOLIDATED into 2026_05_23_154150_create_companies_table
// (business_type already included in CREATE — no separate modifier)
// ══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: business_type is already in the parent CREATE
    }

    public function down(): void
    {
        // No-op
    }
};
