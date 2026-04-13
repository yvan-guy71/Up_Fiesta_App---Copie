<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use FedaPay\FedaPay;
use FedaPay\Transaction;

class PaymentApiController extends Controller
{
    public function createTransaction(Request $request, Booking $booking)
    {
        // Configuration de FedaPay (À mettre dans votre .env normalement)
        FedaPay::setApiKey(config('services.fedapay.secret_key', 'votre_cle_test'));
        FedaPay::setEnvironment(config('services.fedapay.environment', 'sandbox'));

        try {
            $transaction = Transaction::create([
                "description" => "Paiement Réservation Upfiesta #" . $booking->id,
                "amount" => $booking->total_price ?? $booking->price ?? 1000,
                "currency" => ["iso" => "XOF"],
                "callback_url" => route('fedapay.callback'),
                "customer" => [
                    "firstname" => $request->user()->name,
                    "email" => $request->user()->email,
                    "phone_number" => [
                        "number" => $request->user()->phone ?? '',
                        "country" => 'TG' // Togo par défaut pour Flooz/T-Money
                    ]
                ]
            ]);

            $token = $transaction->generateToken();

            return response()->json([
                'url' => $token->url,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}



