<?php

namespace App\Console\Commands;

use App\Models\VehicleEmi;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEmiReminders extends Command
{
    protected $signature = 'emi:send-reminders';

    protected $description = 'Send WhatsApp reminders for EMIs due in 5 days';

    public function handle()
    {
        $this->info('Starting EMI reminder check...');

        // Find EMIs due in exactly 5 days
        $today = Carbon::today();
        $reminderDate = $today->copy()->addDays(5);

        // Get active EMIs where next_due_date is 5 days from today and reminder hasn't been sent
        $upcomingEmis = VehicleEmi::with(['vehicle'])
            ->where('status', 'active')
            ->whereDate('next_due_date', $reminderDate)
            ->where('reminder_sent', false)
            ->get();

        if ($upcomingEmis->isEmpty()) {
            $this->info('No upcoming EMIs to remind.');
            return Command::SUCCESS;
        }

        $whatsAppService = new WhatsAppService();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($upcomingEmis as $emi) {
            try {
                // Get vehicle owner's phone number (you may need to adjust this based on your user model)
                $phoneNumber = $emi->vehicle->user?->phone ?? null;

                if (!$phoneNumber) {
                    $this->warn("No phone number for EMI #{$emi->id}");
                    $failedCount++;
                    continue;
                }

                // Send WhatsApp message
                $sent = $whatsAppService->sendEmiReminder(
                    $phoneNumber,
                    $emi->vehicle->vehicle_number,
                    $emi->emi_amount,
                    $emi->next_due_date
                );

                if ($sent) {
                    // Mark reminder as sent
                    $emi->update([
                        'reminder_sent' => true,
                        'reminder_sent_at' => now(),
                    ]);
                    $this->info("✓ Reminder sent for EMI #{$emi->id}");
                    $sentCount++;
                } else {
                    $this->error("✗ Failed to send reminder for EMI #{$emi->id}");
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("Error processing EMI #{$emi->id}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->info("Completed: {$sentCount} sent, {$failedCount} failed");
        return Command::SUCCESS;
    }
}
