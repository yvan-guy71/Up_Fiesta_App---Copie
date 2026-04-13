<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventApiController extends Controller
{
    public function index()
    {
        return response()->json(Event::with('user.provider', 'user')->latest()->paginate(15));
    }

    public function show(Event $event)
    {
        return response()->json($event->load('user.provider'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string',
            'is_public' => 'boolean',
        ]);

        $event = Auth::user()->events()->create($validated);

        return response()->json([
            'message' => 'Événement créé avec succès.',
            'event' => $event,
        ], 201);
    }

    public function update(Request $request, Event $event)
    {
        if (Auth::id() !== $event->user_id) {
            return response()->json(['error' => 'Action non autorisée.'], 403);
        }

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'event_date' => 'date',
            'location' => 'string',
            'is_public' => 'boolean',
        ]);

        $event->update($validated);

        return response()->json(['message' => 'Événement mis à jour.', 'event' => $event]);
    }

    public function destroy(Event $event)
    {
        if (Auth::id() !== $event->user_id) {
            return response()->json(['error' => 'Action non autorisée.'], 403);
        }

        $event->delete();
        return response()->json(['message' => 'Événement supprimé.']);
    }
}
