<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Contact
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Pages Légales
Route::get('/cgu', [LegalController::class, 'cgu'])->name('legal.cgu');
Route::get('/confidentialite', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/mentions-legales', [LegalController::class, 'legalNotices'])->name('legal.legal-notices');
// Aide
Route::get('/comment-ca-marche', function () {
    return view('help.how-it-works');
})->name('help.how');

Route::get('/locale/{locale}', function (string $locale) {
    $available = array_keys(config('app.available_locales', []));
    if (in_array($locale, $available, true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');
Route::get('/prestataire/{provider}', [HomeController::class, 'showProvider'])->name('providers.show');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request) {
    $user = \App\Models\User::find($request->route('id'));

    if (!$user) {
        abort(404);
    }

    // verify the hash matches
    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // Redirect to appropriate login page based on user role
    if ($user->role === 'provider') {
        return redirect('/prestataire/login')->with('success', 'Votre adresse email a été vérifiée. Vous pouvez maintenant vous connecter à votre espace professionnel.');
    }

    return redirect('/login')->with('success', 'Votre adresse email a été vérifiée. Vous pouvez maintenant vous connecter.');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/resend', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Un nouvel email de vérification a été envoyé.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// notification deletion endpoint
Route::delete('/notifications/{id}', function (\Illuminate\Http\Request $request, $id) {
    $user = $request->user();
    if ($user) {
        $user->notifications()->where('id', $id)->delete();
    }
    return response()->noContent();
})->middleware('auth')->name('notifications.destroy');

// Routes Connexion Google
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

// Routes de réinitialisation de mot de passe
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

Route::get('/inscription', [AuthController::class, 'showClientRegistrationForm'])->name('register.client');
Route::post('/inscription', [AuthController::class, 'registerClient'])->name('register.client.post');

Route::get('/devenir-prestataire', [AuthController::class, 'showRegistrationForm'])->name('register.provider');
Route::post('/devenir-prestataire', [AuthController::class, 'registerProvider'])->name('register.provider.post');

// Catégories de services
Route::get('/categories', [\App\Http\Controllers\ServiceCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [\App\Http\Controllers\ServiceCategoryController::class, 'show'])->name('categories.show');

// Admin notification redirects (only for authenticated admins)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users/{id}', function ($id) {
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            return redirect("/up-fiesta-kygj/users/{$id}/edit");
        }
        abort(403, 'Accès non autorisé');
    });

    Route::get('/admin/service-requests/{id}', function ($id) {
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            return redirect("/up-fiesta-kygj/service-requests/{$id}/edit");
        }
        abort(403, 'Accès non autorisé');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');
    Route::delete('/messages/item/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::delete('/messages/conversation/{user}', [MessageController::class, 'destroyConversation'])->name('messages.conversation.destroy');

    Route::get('/mes-reservations', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/mes-reservations/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/reserver/{provider}', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/reservations/{booking}/avis', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/paiement/{booking}/{method}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/paiement/callback/{booking}', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::post('/paiement/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
    
    Route::get('/demande-service', [ServiceRequestController::class, 'create'])->name('service-requests.create');
    Route::post('/demande-service', [ServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::get('/mes-demandes/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('service-requests.show');

    // provider-specific list & status update
    Route::get('/prestataire/demandes', [ServiceRequestController::class, 'providerIndex'])
        ->name('provider.requests.index');
    Route::post('/prestataire/demandes/{service_request}/status', [ServiceRequestController::class, 'updateStatus'])
        ->name('service-requests.status');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
