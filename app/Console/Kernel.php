<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * All scheduling is defined in routes/console.php.
     * The old artisan commands (emi:send-reminders, vehicle:send-document-reminders)
     * are kept for manual use only — do NOT schedule them here to avoid duplicate sends.
     */
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        // Intentionally empty — see routes/console.php
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
