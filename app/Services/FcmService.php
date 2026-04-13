<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Envoyer une notification Push via Firebase FCM V1 (Méthode Moderne)
     */
    public static function sendPushNotification($token, $title, $body, $data = [])
    {
        try {
            $credentialsPath = base_path('firebase_credentials.json');
            if (!file_exists($credentialsPath)) {
                Log::error("Fichier de configuration Firebase manquant.");
                return null;
            }

            $credentials = json_decode(file_get_stdio($credentialsPath), true);
            $projectId = $credentials['project_id'];

            // 1. Obtenir le jeton d'accès OAuth2
            $accessToken = self::getAccessToken($credentials);

            // 2. Préparer l'URL (FCM V1)
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            // 3. Envoyer la requête
            $response = Http::withToken($accessToken)->post($url, [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data), // FCM V1 n'accepte que des strings dans 'data'
                ],
            ]);

            if ($response->failed()) {
                Log::error("Erreur FCM: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Exception FCM: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Générer un jeton d'accès Google OAuth2 manuellement
     */
    private static function getAccessToken($credentials)
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = '';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $credentials['private_key'], 'SHA256');
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json('access_token');
    }
}

/**
 * Helper simple pour lire le fichier si file_get_contents est bloqué
 */
function file_get_stdio($path) {
    return file_get_contents($path);
}
