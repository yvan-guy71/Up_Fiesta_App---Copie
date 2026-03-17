<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Aucun compte Up Fiesta n\'est associé à cet email Google. Veuillez d\'abord vous inscrire.',
                ]);
            }

            // On vérifie que c'est bien un compte client
            if ($user->role !== 'client') {
                return redirect()->route('login')->withErrors([
                    'email' => 'La connexion Google est réservée aux comptes clients.',
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();

            return redirect()->intended('/')->with('success', 'Ravi de vous revoir via Google, ' . $user->name . ' !');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage());
            
            return redirect()->route('login')->withErrors([
                'email' => 'Désolé, une erreur est survenue lors de la connexion avec Google. Veuillez réessayer.',
            ]);
        }
    }
    public function showRegistrationForm()
    {
        // retrieve categories grouped by kind for display
        $categories = ServiceCategory::orderBy('kind')->orderBy('name')->get();
        $cities = City::all();
        return view('auth.register-provider', compact('categories', 'cities'));
    }

    public function showClientRegistrationForm()
    {
        return view('auth.register-client');
    }

    public function showLoginForm()
    {
        return view('auth.login-client');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required',
            'remember' => 'nullable|boolean',
        ]);

        // normalized credentials
        $identifier = trim($data['email']);
        $password = $data['password'];
        // ensure we always have a boolean to pass to Auth::attempt()
        $remember = $data['remember'] ?? false;

        // Determine if identifier is phone or email
        $isPhone = preg_match('/^\+?[0-9\s\-().]+$/', $identifier) === 1;

        if ($isPhone) {
            $normalized = preg_replace('/[^0-9+]/', '', $identifier);
            $user = \App\Models\User::where('phone', $normalized)->first();
            if ($user) {
                $attempt = Auth::attempt(['email' => $user->email, 'password' => $password], $remember);
            } else {
                $attempt = false;
            }
        } else {
            $attempt = Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
        }

        if ($attempt) {
            $user = Auth::user();

            // if the user tried to sign in through the wrong portal, redirect them
            if ($user->role === 'provider') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Ce compte est un compte professionnel. Veuillez vous connecter via l\'espace pro.',
                ])->with('suggest_provider_login', true);
            }

            // require email verification for everyone else (admins bypass the check via model override)
            if (! $user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Vous devez confirmer votre adresse e-mail avant de vous connecter. Vérifiez votre boîte de réception.',
                ]);
            }

            $request->session()->regenerate();
            
            if ($user->role === 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Ce compte est un compte administrateur. Veuillez vous connecter via l\'interface d\'administration.',
                ]);
            }
            
            return redirect()->intended('/')->with('success', 'Ravi de vous revoir, ' . $user->name . ' !');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function registerClient(Request $request)
    {
        // Normalize phone number for validation
        $normalizedPhone = preg_replace('/[^0-9+]/', '', $request->full_phone ?? $request->phone);
        
        // Check if normalized phone already exists
        if (User::where('phone', $normalizedPhone)->exists()) {
            return back()->withInput()->withErrors([
                'phone' => 'Ce numéro de téléphone est déjà utilisé. Veuillez saisir un numéro différent.',
            ]);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $normalizedPhone,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);

        // notify admins of new registration
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewUserRegisteredNotification($user));
        }

        // send verification email
        $user->sendEmailVerificationNotification();

        // Do not log the user in automatically; they must verify their email first.
        // Redirect back to the login page with a message since the verification notice
        // route is protected by the auth middleware and they are currently a guest.
        return redirect()->route('login')
            ->with('success', 'Bienvenue sur Up Fiesta ! Un email de confirmation vous a été envoyé. Cliquez sur le lien pour activer votre compte, puis connectez-vous.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('info', 'Vous avez été déconnecté.');
    }

    public function registerProvider(Request $request)
    {
        // Normalize phone number for validation
        $normalizedPhone = preg_replace('/[^0-9+]/', '', $request->full_phone ?? $request->phone);
        
        // Check if normalized phone already exists
        if (User::where('phone', $normalizedPhone)->exists()) {
            return back()->withInput()->withErrors([
                'phone' => 'Ce numéro de téléphone est déjà utilisé. Veuillez saisir un numéro différent.',
            ]);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'business_name' => 'required|string|max:255',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:service_categories,id',
            'city_id' => 'required|exists:cities,id',
            'cni_number' => 'required|string|max:50',
            'years_of_experience' => 'required|integer|min:0|max:70',
            'cni_photo_front' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cni_photo_back' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'base_price' => 'required|numeric|min:0',
            'price_range_max' => 'required|numeric|min:0|gte:base_price',
            'description' => 'required|string|min:20',
            'is_company' => 'nullable|boolean',
            'company_registration_number' => 'required_if:is_company,1|nullable|string|max:100',
            'company_proof_doc_front' => 'required_if:is_company,1|nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'company_proof_doc_back' => 'required_if:is_company,1|nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ], [
            'price_range_max.gte' => 'Le prix maximum doit être supérieur ou égal au prix de base.',
            'company_registration_number.required_if' => 'Le numéro RCCM / NIF est obligatoire pour les entreprises.',
            'company_proof_doc_front.required_if' => 'La preuve d\'enregistrement (recto) est obligatoire pour les entreprises.',
            'company_proof_doc_back.required_if' => 'La preuve d\'enregistrement (verso) est obligatoire pour les entreprises.',
        ]);

        $userPhone = $normalizedPhone;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $userPhone,
            'password' => Hash::make($request->password),
            'role' => 'provider',
        ]);
        // admin notification
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewUserRegisteredNotification($user));
        }

        $cniFrontPath = $request->file('cni_photo_front')->store('verification/cni', 'public');
        $cniBackPath = $request->file('cni_photo_back')->store('verification/cni', 'public');
        $logoPath = $request->file('logo')->store('providers/logos', 'public');
        
        $companyProofFrontPath = null;
        $companyProofBackPath = null;
        if ($request->hasFile('company_proof_doc_front')) {
            $companyProofFrontPath = $request->file('company_proof_doc_front')->store('verification/company', 'public');
        }
        if ($request->hasFile('company_proof_doc_back')) {
            $companyProofBackPath = $request->file('company_proof_doc_back')->store('verification/company', 'public');
        }

        $provider = \App\Models\Provider::create([
            'user_id' => $user->id,
            'name' => $request->business_name,
            'email' => $request->email,
            'phone' => $request->full_phone ?? $request->phone,
            'category_id' => $request->category_ids[0], // Store first one as primary for legacy support
            'city_id' => $request->city_id,
            'description' => $request->description,
            'logo' => $logoPath,
            'base_price' => $request->base_price,
            'price_range_max' => $request->price_range_max,
            'cni_number' => $request->cni_number,
            'years_of_experience' => $request->years_of_experience,
            'cni_photo_front' => $cniFrontPath,
            'cni_photo_back' => $cniBackPath,
            'is_company' => $request->has('is_company'),
            'company_registration_number' => $request->company_registration_number,
            'company_proof_doc_front' => $companyProofFrontPath,
            'company_proof_doc_back' => $companyProofBackPath,
            'is_verified' => false,
        ]);

        $provider->categories()->sync($request->category_ids);

        $user->sendEmailVerificationNotification();

        // Do not log the provider in before they verify their email
        // prefer the named login route but fall back to a hard-coded URL when the
        // route is not yet registered (e.g. during unit tests)
        $loginUrl = \Illuminate\Support\Facades\Route::has('filament.provider.auth.login')
            ? route('filament.provider.auth.login')
            : url('/prestataire/login');

        return redirect($loginUrl)
            ->with('info', 'Bienvenue ! Votre compte prestataire a été créé. Un email de confirmation vous a été envoyé. Vérifiez votre boîte pour activer votre compte. Votre profil restera en attente de validation.');
    }
}
