<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Initialise un paiement via PayGate (T-Money, Flooz, carte)
     */
    public static function initializePayment($booking, $method)
    {
        $amount = (int) $booking->total_price;
        $description = "Paiement Upfiesta - Reservation #" . $booking->id;
        $reference = 'UPF-' . $booking->id . '-' . time();
        $currency = 'XOF';

        $payUrl = env('PAYGATE_PAY_URL', 'https://paygateglobal.com/api/v1/pay');
        $merchantId = env('PAYGATE_MERCHANT_ID');
        $apiToken = env('PAYGATE_API_TOKEN');

        $channel = match($method) {
            'tmoney' => 'tmoney',
            'flooz' => 'flooz',
            'card' => 'card',
            default => 'tmoney',
        };

        $callbackUrl = route('payment.callback', ['booking' => $booking->id]);

        try {
            $payload = [
                'merchant' => $merchantId,
                'token' => $apiToken,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'description' => $description,
                'channel' => $channel,
                'callback_url' => $callbackUrl,
            ];

            $response = Http::asForm()->post($payUrl, $payload);

            if ($response->successful() && isset($response['redirect_url'])) {
                Log::info("Paiement initialisé #{$booking->id} via {$channel}");
                return [
                    'success' => true,
                    'transaction_id' => $response['transaction'] ?? $reference,
                    'redirect_url' => $response['redirect_url'],
                ];
            }

            Log::warning('Echec initialisation paiement: ' . $response->body());
            return ['success' => false];
        } catch (\Throwable $e) {
            Log::error('Erreur API PayGate: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Vérifie le statut d'un paiement auprès de PayGate
     */
    public static function verifyPayment($transactionId)
    {
        $statusUrl = env('PAYGATE_STATUS_URL', 'https://paygateglobal.com/api/v1/status');
        $merchantId = env('PAYGATE_MERCHANT_ID');
        $apiToken = env('PAYGATE_API_TOKEN');

        try {
            $response = Http::asForm()->post($statusUrl, [
                'merchant' => $merchantId,
                'token' => $apiToken,
                'transaction' => $transactionId,
            ]);

            if ($response->successful()) {
                return in_array($response['status'] ?? null, ['success', 'paid'], true);
            }
        } catch (\Throwable $e) {
            Log::error('Erreur vérification paiement: ' . $e->getMessage());
        }

        return false;
    }
}



