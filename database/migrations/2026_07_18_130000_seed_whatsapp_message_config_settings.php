<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the default WhatsApp Message Configuration settings.
 *
 * Keys added (group = whatsapp_config):
 *   whatsapp_send_time   — daily send time (HH:MM, IST), default 09:30
 */
return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            [
                'key'   => 'whatsapp_send_time',
                'value' => '09:30',
                'group' => 'whatsapp_config',
                'label' => 'WhatsApp Message Send Time',
            ],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->updateOrInsert(
                ['key' => $row['key']],
                array_merge($row, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['whatsapp_send_time'])->delete();
    }
};
