<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewApiController extends Controller
{
    public function index(Provider $provider)
    {
        return response()->json($provider->reviews()->with('user')->latest()->get());
    }

    public function store(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = $provider->reviews()->updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        return response()->json([
            'message' => 'Avis enregistré avec succès.',
            'review' => $review->load('user'),
        ], 201);
    }
}
