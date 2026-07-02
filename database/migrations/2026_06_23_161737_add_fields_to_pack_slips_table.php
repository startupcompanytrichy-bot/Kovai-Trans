<?php

// ══════════════════════════════════════════════════════════════════
// CONSOLIDATED into 2026_06_23_161221_create_pack_slips_table
// (bill_no, lot_no, quality, folding now in CREATE)
// ══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: fields now in parent CREATE
    }

    public function down(): void
    {
        // No-op
    }
};
