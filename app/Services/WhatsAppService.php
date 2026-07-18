<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected function baileysUrl(): string
    {
        $url = Setting::getValue('whatsapp_baileys_url', '');
        return rtrim($url ?: 'http://localhost:3001', '/');
    }

    /**
     * Read the daily limit fresh from DB every call — never cache in constructor.
     * Default is 1000 (enough for any daily batch).
     */
    protected function getDailyLimit(): int
    {
        return (int) Setting::getValue('whatsapp_daily_limit', 1000);
    }

    public function getReminderNumbers(): array
    {
        $raw = Setting::getValue('whatsapp_reminder_number', '');
        if (!$raw) return [];
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    public function getReminderNumber(): string
    {
        $nums = $this->getReminderNumbers();
        return $nums[0] ?? '';
    }

    public function getRemainingCount(): int
    {
        $dailyLimit = $this->getDailyLimit();
        $lastDate   = Setting::getValue('whatsapp_last_send_date', '');
        $today      = date('Y-m-d');

        if ($lastDate !== $today) {
            // New day — reset counter implicitly
            return $dailyLimit;
        }

        $count = (int) Setting::getValue('whatsapp_today_count', 0);
        return max(0, $dailyLimit - $count);
    }

    public function canSend(): bool
    {
        return $this->getRemainingCount() > 0;
    }

    protected function incrementCounter(): void
    {
        $today    = date('Y-m-d');
        $lastDate = Setting::getValue('whatsapp_last_send_date', '');

        if ($lastDate !== $today) {
            // New day — reset to 1
            Setting::setValue('whatsapp_today_count', '1');
            Setting::setValue('whatsapp_last_send_date', $today);
        } else {
            $count = (int) Setting::getValue('whatsapp_today_count', 0);
            Setting::setValue('whatsapp_today_count', (string) ($count + 1));
        }
    }

    public function sendMessage($phoneNumber, $message)
    {
        try {
            $dailyLimit = $this->getDailyLimit();

            if (!$this->canSend()) {
                Log::warning("WhatsApp daily limit ({$dailyLimit}) reached. Message not sent to {$phoneNumber}.");
                return false;
            }

            $clean = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (strlen($clean) === 10) {
                $clean = '91' . $clean;
            }

            $url      = $this->baileysUrl() . '/send';
            $response = Http::timeout(30)->post($url, [
                'to'      => $clean,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $this->incrementCounter();
                Log::info("WhatsApp sent to {$clean} | today count: " . Setting::getValue('whatsapp_today_count', '?') . "/{$dailyLimit}");
                return true;
            }

            $body  = $response->json();
            $error = $body['error'] ?? $response->body();
            Log::error("Baileys error for {$clean}: {$error}");
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp service exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendToReminderNumber($message)
    {
        $numbers = $this->getReminderNumbers();
        if (empty($numbers)) {
            Log::warning('WhatsApp reminder numbers not configured.');
            return false;
        }
        $allSent = true;
        foreach ($numbers as $number) {
            if (!$this->sendMessage($number, $message)) {
                $allSent = false;
            }
        }
        return $allSent;
    }

    public function getBaileysStatus(): array
    {
        try {
            $resp = Http::timeout(5)->get($this->baileysUrl() . '/status');
            return $resp->successful() ? $resp->json() : ['connected' => false, 'error' => 'Unreachable'];
        } catch (\Exception $e) {
            return ['connected' => false, 'error' => $e->getMessage()];
        }
    }

    public function getConnectedNumber(): ?string
    {
        try {
            $resp = Http::timeout(5)->get($this->baileysUrl() . '/status');
            if ($resp->successful()) {
                $data = $resp->json();
                return $data['number'] ?? null;
            }
        } catch (\Exception $e) {
        }
        return null;
    }

    public function getBaileysQr(): ?string
    {
        try {
            $resp = Http::timeout(5)->get($this->baileysUrl() . '/qr');
            if ($resp->successful()) {
                $data = $resp->json();
                return $data['dataUrl'] ?? $data['qr'] ?? null;
            }
        } catch (\Exception $e) {
        }
        return null;
    }

    /**
     * Send a document (PDF) to a WhatsApp number.
     * Accepts raw PDF bytes (string), encodes to base64 and POSTs to /send-document.
     */
    public function sendDocument(string $phoneNumber, string $pdfContent, string $filename, string $caption = ''): bool
    {
        try {
            $clean = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (strlen($clean) === 10) {
                $clean = '91' . $clean;
            }

            $url      = $this->baileysUrl() . '/send-document';
            $response = Http::timeout(60)->post($url, [
                'to'       => $clean,
                'filename' => $filename,
                'mimetype' => 'application/pdf',
                'base64'   => base64_encode($pdfContent),
                'caption'  => $caption,
            ]);

            if ($response->successful()) {
                $this->incrementCounter();
                Log::info("WhatsApp document sent to {$clean}: {$filename}");
                return true;
            }

            $body  = $response->json();
            $error = $body['error'] ?? $response->body();
            Log::error("Baileys document error for {$clean}: {$error}");
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp sendDocument exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendEmiReminder($phoneNumber, $vehicleNumber, $emiAmount, $dueDate)
    {
        $message  = "🚗 *EMI Payment Reminder*\n\n";
        $message .= "Vehicle: {$vehicleNumber}\n";
        $message .= "Amount Due: ₹" . number_format($emiAmount, 2) . "\n";
        $message .= "Due Date: " . \Carbon\Carbon::parse($dueDate)->format('d M Y') . "\n\n";
        $message .= "Please ensure timely payment to avoid penalties.\n";
        $message .= "Thank you!";

        return $this->sendMessage($phoneNumber, $message);
    }
}
