<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Toutes les catégories pour la zone de recherche (non filtrées par nombre de prestataires)
        $searchCategories = \Illuminate\Support\Facades\Cache::remember('all_categories_search', 3600, function() {
            return ServiceCategory::orderBy('name')->get();
        });

        // 5 catégories avec prestataires pour la section "Parcourir" de la home
        $homeCategories = \Illuminate\Support\Facades\Cache::remember('categories_with_count_home_v2', 3600, function() {
            return ServiceCategory::withCount('providers')->get()->filter(function($category) {
                return $category->providers_count > 0;
            })->take(5);
        });

        // apply kind filter if requested for the home categories (section "Parcourir")
        if ($request->filled('kind')) {
            $kind = $request->kind;
            if (in_array($kind, [ServiceCategory::KIND_PRESTATIONS, ServiceCategory::KIND_DOMESTIQUES], true)) {
                $homeCategories = ServiceCategory::withCount('providers')
                    ->where('kind', $kind)
                    ->get()
                    ->filter(function($category) {
                        return $category->providers_count > 0;
                    })
                    ->take(5);
            }
        }

        // if a specific category was clicked, only keep that one for the section "Parcourir"
        if ($request->filled('category')) {
            $homeCategories = $homeCategories->where('id', $request->category);
        }
        
        $cities = \Illuminate\Support\Facades\Cache::remember('all_cities', 3600, function() {
            return City::all();
        });

        // Mise en cache des prestataires à la une
        $featuredProviders = \Illuminate\Support\Facades\Cache::remember('featured_providers_home_v2', 1800, function() {
            return Provider::with(['category', 'city'])->where('is_verified', true)->latest()->take(15)->get();
        });
        
        return view('welcome', [
            'searchCategories' => $searchCategories,
            'categories' => $searchCategories,  // Alias pour compatibilité avec la vue
            'homeCategories' => $homeCategories,
            'cities' => $cities,
            'featuredProviders' => $featuredProviders
        ]);
    }

    public function search(Request $request)
    {
        try {
            // Get categories and cities without heavy caching
            $searchCategories = ServiceCategory::orderBy('name')->get();
            $cities = City::all();
            
            // Start with base query
            $query = Provider::query();

            // Search by keyword
            if ($request->filled('q')) {
                $searchTerm = '%' . $request->q . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
                });
            }

            // Filter by kind
            if ($request->filled('kind')) {
                $kind = $request->kind;
                if (in_array($kind, ['prestations', 'domestiques'], true)) {
                    $query->whereHas('category', function ($sq) use ($kind) {
                        $sq->where('kind', '=', $kind);
                    });
                }
            }

            // Filter by category
            if ($request->filled('category')) {
                $categoryId = (int)$request->category;
                $query->where('category_id', '=', $categoryId);
            }

            // Filter by city
            if ($request->filled('city')) {
                $query->where('city_id', '=', (int)$request->city);
            }

            // Get the total count before pagination
            $totalResults = $query->count();
            
            // Load relations, order and paginate
            $providers = $query->with(['category', 'city'])->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
            
            // Get selected filter values for display
            $selectedCategory = null;
            $selectedCity = null;
            
            if ($request->filled('category')) {
                $selectedCategory = $searchCategories->firstWhere('id', (int) $request->category);
            }
            
            if ($request->filled('city')) {
                $selectedCity = $cities->firstWhere('id', (int) $request->city);
            }

            return view('search', [
                'providers' => $providers,
                'searchCategories' => $searchCategories,
                'cities' => $cities,
                'totalResults' => $totalResults,
                'selectedCategory' => $selectedCategory,
                'selectedCity' => $selectedCity
            ]);

        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            
            return view('search', [
                'providers' => collect(),
                'searchCategories' => ServiceCategory::orderBy('name')->get() ?? collect(),
                'cities' => City::all() ?? collect(),
                'totalResults' => 0,
                'selectedCategory' => null,
                'selectedCity' => null
            ]);
        }
    }

    public function showProvider(Provider $provider)
    {
        $provider->load(['category', 'city', 'reviews.user']);
        return view('providers.show', compact('provider'));
    }
}
