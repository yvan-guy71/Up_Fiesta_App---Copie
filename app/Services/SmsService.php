<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoie un SMS via une API locale/régionale (ex: Termii, Twilio ou API Togo local)
     * Pour cet exemple, on simule l'intégration d'un provider courant en Afrique de l'Ouest.
     */
    public static function send($to, $message)
    {
        // Nettoyage du numéro (ex: +22890...)
        $to = preg_replace('/[^0-9+]/', '', $to);

        Log::info("Envoi SMS à {$to}: {$message}");

        // Simulation d'appel API
        /*
        Http::post('https://api.sms-provider.com/send', [
            'api_key' => config('services.sms.key'),
            'to' => $to,
            'from' => 'UpFiesta',
            'message' => $message,
        ]);
        */

        return true;
    }

    public static function notifyNewBooking($booking)
    {
        $provider = $booking->provider;
        $message = "Up Fiesta: Nouvelle demande de reservation pour le " . $booking->event_date->format('d/m/Y') . ". Connectez-vous pour confirmer.";
        
        if ($provider->phone) {
            self::send($provider->phone, $message);
        }
    }
}
