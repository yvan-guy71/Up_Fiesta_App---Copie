<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ServiceCategory;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProviderApiController extends Controller
{
    public function index(Request $request)
    {
        // On ne montre que les prestataires approuvés
        $query = Provider::query()->where('verification_status', 'approved');

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('city_id') && !empty($request->city_id)) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // On charge les relations nécessaires pour l'UI Flutter
        $providers = $query->with(['category', 'city', 'reviews'])->latest()->paginate(10);

        return response()->json($providers);
    }

    public function categories()
    {
        $categories = ServiceCategory::select('id', 'name', 'slug')->get();
        Log::info('Categories returned: ' . $categories->count());
        return response()->json($categories);
    }

    public function cities()
    {
        $cities = City::select('id', 'name', 'country_id')->get();
        Log::info('Cities returned: ' . $cities->count());
        return response()->json($cities);
    }

    public function show(Provider $provider)
    {
        // On s'assure de charger tout ce qu'il faut pour l'écran de détails
        return response()->json($provider->load(['category', 'city', 'reviews.user', 'media']));
    }

    /**
     * Demander un changement de prix (Soumis à validation admin)
     */
    public function requestPriceChange(Request $request)
    {
        $request->validate([
            'pending_base_price' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();
        $provider = $user->provider;

        if (!$provider) {
            return response()->json(['error' => 'Profil prestataire non trouvé.'], 404);
        }

        $provider->update([
            'pending_base_price' => $request->pending_base_price,
            'price_change_status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Votre demande de changement de prix a été envoyée à l\'administration.',
            'status' => 'pending'
        ]);
    }
}
