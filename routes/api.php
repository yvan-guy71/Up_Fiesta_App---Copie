<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProviderApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\MessageApiController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\VerificationApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Authentification Publique ---
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/forgot-password', [AuthApiController::class, 'sendResetLinkEmail']);
Route::post('/auth/google/callback', [AuthApiController::class, 'handleGoogleCallback']);

// --- Consultation Publique ---
Route::get('/providers', [ProviderApiController::class, 'index']);
Route::get('/categories', [ProviderApiController::class, 'categories']);
Route::get('/cities', [ProviderApiController::class, 'cities']); // AJOUTÉ
Route::get('/providers/{provider}', [ProviderApiController::class, 'show']);
Route::get('/providers/{provider}/reviews', [ReviewApiController::class, 'index']); // AJOUTÉ
Route::get('/events', [EventApiController::class, 'index']); // AJOUTÉ
Route::get('/events/{event}', [EventApiController::class, 'show']); // AJOUTÉ

// --- Routes Protégées (Nécessitent un Token) ---
Route::middleware('auth:sanctum')->group(function () {
    // Profil & Support
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::put('/me', [AuthApiController::class, 'update']);
    Route::post('/update-fcm-token', [AuthApiController::class, 'updateFcmToken']);
    Route::get('/support-user', [AuthApiController::class, 'getAdmin']);

    // Réservations
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::post('/bookings/{provider}', [BookingApiController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingApiController::class, 'show']);
    Route::patch('/bookings/{booking}/status', [BookingApiController::class, 'updateStatus']);
    Route::post('/bookings/{booking}/accept', [BookingApiController::class, 'acceptBooking']);
    Route::post('/bookings/{booking}/reject', [BookingApiController::class, 'rejectBooking']);

    // Messagerie
    Route::get('/messages', [MessageApiController::class, 'index']);
    Route::get('/messages/{user}', [MessageApiController::class, 'show']);
    Route::post('/messages/{user}', [MessageApiController::class, 'store']);

    // Avis & Événements (Protégés)
    Route::post('/providers/{provider}/reviews', [ReviewApiController::class, 'store']); // AJOUTÉ
    Route::apiResource('/user-events', EventApiController::class)->except(['index', 'show']); // AJOUTÉ pour les providers

    // Dashboard Prestataire
    Route::get('/provider/stats', [ProviderApiController::class, 'stats']);
    Route::post('/provider/media', [ProviderApiController::class, 'uploadMedia']);
    Route::delete('/provider/media/{media}', [ProviderApiController::class, 'deleteMedia']);
    Route::post('/provider/request-price-change', [ProviderApiController::class, 'requestPriceChange']); // NOUVEAU

    // Vérification Prestataire
    Route::get('/provider/verification/status', [VerificationApiController::class, 'status']);
    Route::post('/provider/verification/submit', [VerificationApiController::class, 'submit']);
});
