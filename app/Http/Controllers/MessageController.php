<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Message::where(function($q) use ($user) {
                $q->where('receiver_id', $user->id)->where('deleted_for_receiver', false);
            })
            ->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)->where('deleted_for_sender', false);
            });

        $messages = $query->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function($message) {
                return $message->sender_id == Auth::id() ? $message->receiver_id : $message->sender_id;
            });

        return view('messages.index', compact('messages'));
    }

    public function show(Request $request, $userId)
    {
        $user = Auth::user();
        $contact = User::findOrFail($userId);

        // ENFORCE STRICT ADMIN INTERMEDIARY: 
        // - Clients can ONLY contact admin, NEVER providers (even after assignment)
        // - Providers can ONLY contact admin, NEVER clients (even after assignment)
        
        if ($user->role === 'client' && $contact->role === 'provider') {
            return redirect()->route('messages.index')
                ->with('error', 'Vous pouvez exprimer vos besoins auprès de Up-fiesta. Up-fiesta se charge d\'assigner les prestataires.');
        }

        if ($user->role === 'provider' && $contact->role === 'client') {
            return redirect()->route('messages.index')
                ->with('error', 'Vous pouvez communiquer uniquement avec l\'administration de Up-fiesta.');
        }

        $messages = Message::where(function($q) use ($user, $userId) {
                $q->where('sender_id', $user->id)->where('receiver_id', $userId)->where('deleted_for_sender', false);
            })
            ->orWhere(function($q) use ($user, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $user->id)->where('deleted_for_receiver', false);
            })
            ->with(['provider.category', 'provider.city'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $user->id)
            ->update(['is_read' => true]);

        $providers = [];
        if ($user->role === 'admin' && $contact->role === 'client') {
            $providers = Provider::with(['category', 'city'])->get();
        }

        $prefillMessage = '';
        if ($request->has('needs_provider')) {
            $provider = Provider::find($request->needs_provider);
            if ($provider) {
                $prefillMessage = "Bonjour, je suis intéressé par les services du prestataire \"" . $provider->name . "\". J'aimerais avoir plus d'informations pour mes besoins.";
            }
        }

        return view('messages.show', compact('contact', 'messages', 'providers', 'prefillMessage'));
    }

    public function store(Request $request, $userId)
    {
        $sender = Auth::user();
        $receiver = User::findOrFail($userId);

        // ENFORCE STRICT ADMIN INTERMEDIARY:
        // - Clients can ONLY contact admin, NEVER providers (even after assignment)
        // - Providers can ONLY contact admin, NEVER clients (even after assignment)
        
        if ($sender->role === 'client' && $receiver->role === 'provider') {
            return back()->with('error', 'Vous devez exprimer vos besoins auprès de Up-fiesta. Up-fiesta se charge d\'assigner les prestataires.');
        }

        if ($sender->role === 'provider' && $receiver->role === 'client') {
            return back()->with('error', 'Vous pouvez communiquer uniquement avec l\'administration de Up-fiesta.');
        }

        $request->validate([
            'content' => 'required_without:provider_id|nullable|string',
            'provider_id' => 'nullable|exists:providers,id',
        ]);

        // Restriction : L'admin ne peut proposer des prestataires qu'aux clients
        $providerId = $request->input('provider_id');
        if ($sender->role === 'admin' && $providerId && $receiver->role !== 'client') {
            return back()->with('error', 'Vous ne pouvez proposer des prestataires qu\'aux clients.');
        }

        $messageContent = (string) ($request->input('content') ?? '');

        if ($providerId) {
            $provider = Provider::find($providerId);
            if ($provider) {
                $recommendation = "Je vous propose ce prestataire : " . $provider->name;
                $messageContent = $messageContent !== '' ? $messageContent . "\n\n" . $recommendation : $recommendation;
            }
        }

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'content' => $messageContent,
            'provider_id' => $providerId,
        ]);

        return back()->with('success', 'Message envoyé !');
    }

    public function destroy(Message $message)
    {
        $user = Auth::user();

        if ($message->sender_id === $user->id) {
            $message->update(['deleted_for_sender' => true]);
            $contactId = $message->receiver_id;
        } elseif ($message->receiver_id === $user->id) {
            $message->update(['deleted_for_receiver' => true]);
            $contactId = $message->sender_id;
        } else {
            return back()->with('error', 'Action non autorisée.');
        }

        return redirect()->route('messages.show', $contactId)->with('success', 'Message supprimé pour vous.');
    }

    public function destroyConversation($userId)
    {
        $user = Auth::user();
        $contact = User::findOrFail($userId);

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $contact->id)
            ->update(['deleted_for_sender' => true]);

        Message::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->update(['deleted_for_receiver' => true]);

        return redirect()->route('messages.index')->with('success', 'Conversation masquée pour vous.');
    }
}
