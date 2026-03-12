<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Provider;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

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
        
        $query = Provider::query()->with(['categories', 'category', 'city']);

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhereHas('categories', function($sq) use ($request) {
                      $sq->where('name', 'like', '%' . $request->q . '%');
                  });
            });
        }

        if ($request->filled('kind')) {
            $kind = $request->kind;
            if (in_array($kind, [\App\Models\ServiceCategory::KIND_PRESTATIONS, \App\Models\ServiceCategory::KIND_DOMESTIQUES], true)) {
                $query->where(function($q) use ($kind) {
                    $q->whereHas('category', function ($sq) use ($kind) {
                        $sq->where('kind', $kind);
                    })->orWhereHas('categories', function ($sq) use ($kind) {
                        $sq->where('kind', $kind);
                    });
                });
            }
        }

        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->where(function($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('categories', function ($sq) use ($categoryId) {
                      $sq->where('service_categories.id', $categoryId);
                  });
            });
        }

        if ($request->filled('city')) {
            $query->where('city_id', $request->city);
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Optimisation : eager load only necessary relations and paginate
        $providers = $query->latest()->paginate(50)->withQueryString();
        
        // Mise en cache des prestataires à la une
        $featuredProviders = \Illuminate\Support\Facades\Cache::remember('featured_providers_home_v2', 1800, function() {
            return Provider::with(['category', 'city'])->where('is_verified', true)->latest()->take(15)->get();
        });
        
        return view('welcome', compact('searchCategories', 'homeCategories', 'cities', 'providers', 'featuredProviders'));
    }

    public function showProvider(Provider $provider)
    {
        $provider->load(['category', 'city', 'reviews.user']);
        return view('providers.show', compact('provider'));
    }
}
