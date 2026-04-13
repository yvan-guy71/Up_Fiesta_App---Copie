<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Provider;
use App\Models\User;
use App\Notifications\AdminNewBookingNotification;
use App\Notifications\ClientBookingAcceptedNotification;
use App\Notifications\ClientBookingRejectedNotification;
use App\Notifications\ProviderNewBookingNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class BookingApiController extends Controller
{
    /**
     * Liste des réservations de l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();

        $query = Booking::query();

        if ($user->role === 'provider') {
            // Si c'est un prestataire, on voit les réservations qui lui sont adressées
            $query->whereHas('provider', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            // Si c'est un client, on voit ses propres réservations
            $query->where('user_id', $user->id);
        }

        $bookings = $query->with(['provider.user', 'user', 'assignedService.service'])
            ->latest()
            ->paginate(15);

        return response()->json($bookings);
    }

    /**
     * Créer une nouvelle réservation
     */
    public function store(Request $request, Provider $provider)
    {
        $request->validate([
            'booking_date' => 'required|date|after:today',
            'message' => 'nullable|string',
        ]);

        // Vérifier que le prestataire existe et est un provider
        if (!$provider->user_id) {
            return response()->json(['message' => 'Prestataire non trouvé.'], 404);
        }

        // Créer la réservation
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'provider_id' => $provider->id,
            'event_date' => $request->booking_date,
            'event_details' => $request->message,
            'status' => 'pending_provider_response',
            'payment_status' => 'pending',
        ]);

        // NOTIFICATION AU PRESTATAIRE
        $providerUser = $provider->user;
        if ($providerUser) {
            // Notification database + email
            $providerUser->notify(new ProviderNewBookingNotification($booking));

            // FCM push notification si disponible
            if ($providerUser->fcm_token) {
                FcmService::sendPushNotification(
                    $providerUser->fcm_token,
                    "Nouvelle demande de réservation 🎊",
                    "Un client souhaite réserver vos services pour le " . date('d/m/Y', strtotime($request->booking_date)),
                    ['booking_id' => (string) $booking->id, 'type' => 'new_booking']
                );
            }
        }

        // NOTIFICATION AUX ADMINS (information seulement)
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new AdminNewBookingNotification($booking));
        }

        return response()->json([
            'message' => 'Réservation envoyée avec succès.',
            'booking' => $booking->load('provider'),
            'provider_verified' => $provider->is_verified, // Inclure info de vérification
        ], 201);
    }

    /**
     * Détails d'une réservation
     */
    public function show(Booking $booking)
    {
        // Vérification de propriété
        if ($booking->user_id !== Auth::id() && $booking->provider->user_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($booking->load(['provider.user', 'user', 'assignedService', 'review']));
    }

    /**
     * Mettre à jour le statut (Confirmer/Annuler)
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed'
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        // NOTIFICATION AU CLIENT : Changement de statut
        if ($oldStatus !== $request->status) {
            $client = $booking->user;
            if ($client && $client->fcm_token) {
                $statusMessages = [
                    'confirmed' => "Votre réservation a été confirmée ! ✅",
                    'cancelled' => "Désolé, votre réservation a été annulée. ❌",
                    'completed' => "Prestation terminée. N'oubliez pas de laisser un avis ! ⭐",
                ];

                FcmService::sendPushNotification(
                    $client->fcm_token,
                    "Mise à jour de votre réservation",
                    $statusMessages[$request->status] ?? "Le statut de votre réservation a changé.",
                    ['booking_id' => (string) $booking->id, 'type' => 'booking_status']
                );
            }
        }

        return response()->json([
            'message' => 'Statut mis à jour avec succès.',
            'booking' => $booking
        ]);
    }

    /**
     * Le prestataire accepte la réservation
     */
    public function acceptBooking(Request $request, Booking $booking)
    {
        // Vérification que le prestataire qui accepte est bien le propriétaire de la reservation
        $user = Auth::user();
        if ($booking->provider->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        // La reservation ne doit pas avoir déjà reçu une réponse
        if ($booking->status !== 'pending_provider_response') {
            return response()->json(['message' => 'Cette réservation a déjà reçu une réponse.'], 400);
        }

        // Marquer la réservation comme acceptée
        $booking->update([
            'status' => 'confirmed',
            'provider_response_at' => now(),
        ]);

        // Notifier le client que le prestataire a accepté
        $client = $booking->user;
        if ($client) {
            $client->notify(new ClientBookingAcceptedNotification($booking));

            // FCM push notification si disponible
            if ($client->fcm_token) {
                FcmService::sendPushNotification(
                    $client->fcm_token,
                    "Réservation acceptée ✅",
                    $booking->provider->name . " a accepté votre demande!",
                    ['booking_id' => (string) $booking->id, 'type' => 'booking_accepted']
                );
            }
        }

        return response()->json([
            'message' => 'Réservation acceptée avec succès.',
            'booking' => $booking->load('provider', 'user')
        ]);
    }

    /**
     * Le prestataire refuse la réservation
     */
    public function rejectBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Vérification que le prestataire qui refuse est bien le propriétaire de la reservation
        $user = Auth::user();
        if ($booking->provider->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        // La reservation ne doit pas avoir déjà reçu une réponse
        if ($booking->status !== 'pending_provider_response') {
            return response()->json(['message' => 'Cette réservation a déjà reçu une réponse.'], 400);
        }

        // Marquer la réservation comme refusée
        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason'),
            'provider_response_at' => now(),
        ]);

        // Notifier le client que le prestataire a refusé
        $client = $booking->user;
        if ($client) {
            $client->notify(new ClientBookingRejectedNotification($booking));

            // FCM push notification si disponible
            if ($client->fcm_token) {
                FcmService::sendPushNotification(
                    $client->fcm_token,
                    "Réservation refusée ❌",
                    $booking->provider->name . " a refusé votre demande.",
                    ['booking_id' => (string) $booking->id, 'type' => 'booking_rejected']
                );
            }
        }

        return response()->json([
            'message' => 'Réservation refusée avec succès.',
            'booking' => $booking->load('provider', 'user')
        ]);
    }
}
