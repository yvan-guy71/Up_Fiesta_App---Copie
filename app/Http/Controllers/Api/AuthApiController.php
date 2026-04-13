<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthApiController extends Controller
{
    /**
     * Connexion classique par email/password
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => $user,
        ]);
    }

    /**
     * Inscription classique
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:client,provider',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user,
        ]);
    }

    /**
     * Gestion de la connexion Google (Socialite)
     */
    public function handleGoogleCallback(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'device_name' => 'required',
        ]);

        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->token);

            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(),
                'role' => 'client',
                'password' => Hash::make(str()->random(24)),
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'token' => $user->createToken($request->device_name)->plainTextToken,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Échec de l\'authentification Google : ' . $e->getMessage()], 401);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    /**
     * Récupérer l'utilisateur actuel
     */
    public function me(Request $request)
    {
        return response()->json($request->user()->load('provider'));
    }

    /**
     * Mettre à jour le profil
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user->load('provider'),
        ]);
    }

    /**
     * Mettre à jour le token FCM pour les notifications
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $request->user()->update([
            'fcm_token' => $request->fcm_token
        ]);

        return response()->json(['message' => 'Token FCM mis à jour.']);
    }

    /**
     * Récupérer l'administrateur pour le support
     */
    public function getAdmin()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }
        return response()->json($admin);
    }

    /**
     * Envoi du lien de réinitialisation par email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Lien de réinitialisation envoyé par email.']);
        }

        return response()->json(['message' => 'Impossible d\'envoyer l\'email.'], 400);
    }
}
