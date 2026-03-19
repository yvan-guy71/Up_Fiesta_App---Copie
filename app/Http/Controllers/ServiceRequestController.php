<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\ServiceRequestCreatedNotification;
use App\Notifications\ServiceRequestStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ServiceRequestController extends Controller
{
    public function create(Request $request)
    {
        if (Auth::check() && Auth::user()->role !== 'client') {
            return redirect('/')->with('error', 'Seuls les clients peuvent exprimer leurs besoins.');
        }

        $selectedProvider = null;
        if ($request->has('provider_id')) {
            $selectedProvider = Provider::find($request->provider_id);

            if ($selectedProvider && ! $selectedProvider->is_verified) {
                session()->flash('warning', 'Ce prestataire n\'est pas encore vérifié par Up Fiesta. Vous pouvez continuer, mais nous vous recommandons de privilégier les prestataires vérifiés.');
            }
        }

        // optional kind filter (prestations/domestiques)
        $kind = $request->query('kind');
        $query = Provider::with(['categories', 'category', 'city']);
        if (in_array($kind, [\App\Models\ServiceCategory::KIND_PRESTATIONS, \App\Models\ServiceCategory::KIND_DOMESTIQUES], true)) {
            $query->where(function($q) use ($kind) {
                $q->whereHas('category', function ($sq) use ($kind) {
                    $sq->where('kind', $kind);
                })->orWhereHas('categories', function ($sq) use ($kind) {
                    $sq->where('kind', $kind);
                });
            });
        }
        $providers = $query->get();

        return view('service_requests.create', compact('selectedProvider', 'providers', 'kind'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type') === 'event' ? 'event' : 'service';
        $kind = in_array($request->input('kind'), [\App\Models\ServiceRequest::KIND_DOMESTIQUES, \App\Models\ServiceRequest::KIND_PRESTATIONS])
            ? $request->input('kind')
            : \App\Models\ServiceRequest::KIND_PRESTATIONS;

        $rules = [
            'type' => 'nullable|in:service,event',
            'kind' => 'nullable|in:prestations,domestiques',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'budget' => 'required|numeric|min:500',
        ];

        if ($type === 'event') {
            $rules['provider_ids'] = 'nullable|array';
            $rules['provider_ids.*'] = 'exists:providers,id';
        } else {
            $rules['provider_id'] = 'nullable|exists:providers,id';
        }

        $validated = $request->validate($rules);

        try {
            $providerId = $validated['provider_id'] ?? null;
            $description = $validated['description'];

            if ($type === 'event' && ! empty($validated['provider_ids'])) {
                $selectedProviders = Provider::with(['category', 'city'])
                    ->whereIn('id', $validated['provider_ids'])
                    ->get();

                if ($selectedProviders->isNotEmpty()) {
                    $list = $selectedProviders->map(function ($provider) {
                        $category = $provider->category ? $provider->category->name : 'Sans catégorie';
                        $city = $provider->city ? $provider->city->name : 'Ville non renseignée';
                        return $provider->name.' ('.$category.' - '.$city.')';
                    })->implode(', ');

                    $description .= "\n\nPrestataires sélectionnés par le client pour cet événement : ".$list;
                }

                $providerId = null;
            }

            $serviceRequest = ServiceRequest::create([
                'user_id' => Auth::id(),
                'type' => $type,
                'kind' => $kind,
                'provider_id' => $providerId,
                'event_id' => null,
                'subject' => $validated['subject'],
                'description' => $description,
                'status' => 'pending',
                'event_date' => $validated['event_date'] ?? null,
                'location' => $validated['location'] ?? null,
                'budget' => $validated['budget'] ?? null,
            ]);

            // notifications: ONLY client and admin (not provider until admin assigns it)
            $serviceRequest->user->notify(new ServiceRequestCreatedNotification($serviceRequest));

            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new ServiceRequestCreatedNotification($serviceRequest, true));
                
                // Also send a message in the panel for each admin
                foreach ($admins as $admin) {
                    \App\Models\Message::create([
                        'sender_id' => Auth::id(),
                        'receiver_id' => $admin->id,
                        'content' => "Nouvelle demande de " . ($type === 'event' ? "d'organisation d'événement" : "service") . " : " . $serviceRequest->subject . ". " . $description,
                    ]);
                }
            }

            // Provider will be notified only when admin assigns the service to them

            $message = $type === 'event'
                ? "Votre demande d'événement a bien été envoyée. Up-Fiesta se charge de tout, nous contactons les prestataires pour vous."
                : "Votre demande de service a bien été envoyée. Up-Fiesta se charge de tout, nous contactons les prestataires pour vous.";

            return redirect()
                ->route('home')
                ->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de la création de la demande de service', [
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', "Une erreur est survenue lors de l'envoi de votre demande. Merci de réessayer ou de contacter Up-Fiesta.");
        }
    }

    /**
     * Display a specific service request
     */
    public function show(ServiceRequest $serviceRequest)
    {
        // Check if user owns this service request or is admin/provider involved
        $user = Auth::user();
        
        if ($user->role === 'client' && $serviceRequest->user_id !== $user->id) {
            abort(403);
        } elseif ($user->role === 'provider' && $serviceRequest->provider_id !== $user->provider?->id) {
            abort(403);
        }

        $serviceRequest->load(['user', 'provider']);
        
        return view('service_requests.show', compact('serviceRequest'));
    }

    /**
     * Display a list of requests assigned to the authenticated provider.
     */
    public function providerIndex()
    {
        $user = Auth::user();
        if ($user->role !== 'provider') {
            abort(403);
        }
        $provider = $user->provider;
        if (! $provider) {
            return redirect()->route('home')->with('error', 'Profil prestataire introuvable.');
        }

        $requests = ServiceRequest::where('provider_id', $provider->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('provider.requests.index', compact('requests'));
    }

    /**
     * Provider or admin changes status of a request.
     */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        $isProvider = $user->role === 'provider' && $user->provider && $user->provider->id === $serviceRequest->provider_id;

        if (! ($isAdmin || $isProvider)) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $serviceRequest->status = $validated['status'];
        $serviceRequest->save();

        // notify client
        $serviceRequest->user->notify(new ServiceRequestStatusNotification($serviceRequest, $validated['status']));

        // notify admins
        $admins = User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new ServiceRequestStatusNotification($serviceRequest, $validated['status'], true));
        }

        return back()->with('success', 'Statut mis à jour.');
    }
}
