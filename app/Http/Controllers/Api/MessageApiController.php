<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageApiController extends Controller
{
    // Liste des conversations
    public function index()
    {
        $user = Auth::user();

        // Récupère les derniers messages de chaque conversation
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($user) {
                return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($msgs) {
                return $msgs->first();
            })
            ->values();

        return response()->json($conversations);
    }

    // Messages d'une conversation spécifique
    public function show(User $user)
    {
        $me = Auth::user();
        $messages = Message::where(function ($q) use ($me, $user) {
            $q->where('sender_id', $me->id)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($me, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $me->id);
        })->oldest()->get();

        return response()->json($messages);
    }

    // Envoyer un message
    public function store(Request $request, User $user)
    {
        $request->validate(['content' => 'required|string']);

        $me = Auth::user();
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        // ENVOYER UNE NOTIFICATION AU DESTINATAIRE
        if ($user->fcm_token) {
            FcmService::sendPushNotification(
                $user->fcm_token,
                "Nouveau message de {$me->name} ✉️",
                $request->content,
                [
                    'sender_id' => (string) $me->id,
                    'type' => 'chat_message'
                ]
            );
        }

        return response()->json($message, 201);
    }
}
