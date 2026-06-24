<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiKey;
    protected $phoneNumberId;
    protected $businessAccountId;

    public function __construct()
    {
        // Use environment variables or placeholder values for WhatsApp API
        // You'll need to configure these in your .env file
        $this->apiKey = env('WHATSAPP_API_KEY', '');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID', '');
        $this->businessAccountId = env('WHATSAPP_BUSINESS_ACCOUNT_ID', '');
    }

    /**
     * Send WhatsApp message to a phone number
     * 
     * @param string $phoneNumber Phone number with country code (e.g., 919876543210)
     * @param string $message Message text
     * @return bool
     */
    public function sendMessage($phoneNumber, $message)
    {
        try {
            // If API key is not configured, log and return false
            if (!$this->apiKey) {
                Log::warning("WhatsApp API not configured. Message to {$phoneNumber}: {$message}");
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("https://graph.instagram.com/v18.0/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $phoneNumber,
                'type' => 'text',
                'text' => [
                    'preview_url' => true,
                    'body' => $message,
                ],
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent to {$phoneNumber}");
                return true;
            }

            Log::error("WhatsApp API error: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp service error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send EMI reminder notification
     * 
     * @param string $phoneNumber Customer phone number
     * @param string $vehicleNumber Vehicle registration number
     * @param float $emiAmount EMI amount
     * @param string $dueDate Due date (Y-m-d format)
     * @return bool
     */
    public function sendEmiReminder($phoneNumber, $vehicleNumber, $emiAmount, $dueDate)
    {
        $message = "🚗 *EMI Payment Reminder*\n\n";
        $message .= "Vehicle: {$vehicleNumber}\n";
        $message .= "Amount Due: ₹" . number_format($emiAmount, 2) . "\n";
        $message .= "Due Date: " . \Carbon\Carbon::parse($dueDate)->format('d M Y') . "\n\n";
        $message .= "Please ensure timely payment to avoid penalties.\n";
        $message .= "Thank you!";

        return $this->sendMessage($phoneNumber, $message);
    }
}
