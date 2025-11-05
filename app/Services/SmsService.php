<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $provider;
    protected $apiKey;
    protected $apiSecret;
    protected $senderId;

    public function __construct()
    {
        $this->provider = config('sms.provider', 'twilio'); // Default to Twilio
        $this->apiKey = config('sms.api_key');
        $this->apiSecret = config('sms.api_secret');
        $this->senderId = config('sms.sender_id');
    }

    /**
     * Send SMS using the configured provider
     *
     * @param string|array $to Phone number(s) to send to
     * @param string $message The message content
     * @return array Response with status and message
     */
    public function sendSms($to, string $message): array
    {
        $recipients = is_array($to) ? $to : [$to];

        try {
            switch ($this->provider) {
                case 'twilio':
                    return $this->sendViaTwilio($recipients, $message);
                case 'victorylink':
                    return $this->sendViaVictoryLink($recipients, $message);
                default:
                    return [
                        'success' => false,
                        'message' => 'Unsupported SMS provider',
                        'error' => 'Provider not configured'
                    ];
            }
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(array $recipients, string $message): array
    {
        $successCount = 0;
        $errors = [];

        foreach ($recipients as $phone) {
            try {
                $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->apiKey}/Messages.json", [
                        'To' => $this->formatPhoneNumber($phone),
                        'From' => $this->senderId,
                        'Body' => $message,
                    ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $errors[] = "Failed to send to {$phone}: " . $response->body();
                }
            } catch (\Exception $e) {
                $errors[] = "Error sending to {$phone}: " . $e->getMessage();
            }
        }

        return [
            'success' => $successCount > 0,
            'message' => "Sent to {$successCount} of " . count($recipients) . " recipients",
            'errors' => $errors
        ];
    }

    /**
     * Send SMS via VictoryLink
     */
    protected function sendViaVictoryLink(array $recipients, string $message): array
    {
        // VictoryLink API implementation
        // This is a placeholder - actual implementation depends on VictoryLink API documentation
        $successCount = 0;
        $errors = [];

        foreach ($recipients as $phone) {
            try {
                $response = Http::post('https://api.victorylink.com/sms/send', [
                    'api_key' => $this->apiKey,
                    'api_secret' => $this->apiSecret,
                    'to' => $this->formatPhoneNumber($phone),
                    'from' => $this->senderId,
                    'message' => $message,
                ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $errors[] = "Failed to send to {$phone}: " . $response->body();
                }
            } catch (\Exception $e) {
                $errors[] = "Error sending to {$phone}: " . $e->getMessage();
            }
        }

        return [
            'success' => $successCount > 0,
            'message' => "Sent to {$successCount} of " . count($recipients) . " recipients",
            'errors' => $errors
        ];
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Add country code if not present (assuming Saudi Arabia +966)
        if (!str_starts_with($phone, '+')) {
            if (str_starts_with($phone, '0')) {
                $phone = '+966' . substr($phone, 1);
            } else {
                $phone = '+966' . $phone;
            }
        }

        return $phone;
    }

    /**
     * Check if SMS service is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->apiSecret) && !empty($this->senderId);
    }

    /**
     * Get SMS balance (if supported by provider)
     */
    public function getBalance(): ?float
    {
        try {
            switch ($this->provider) {
                case 'twilio':
                    $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
                        ->get("https://api.twilio.com/2010-04-01/Accounts/{$this->apiKey}/Balance.json");

                    if ($response->successful()) {
                        $data = $response->json();
                        return (float) $data['balance'];
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get SMS balance: ' . $e->getMessage());
        }

        return null;
    }
}