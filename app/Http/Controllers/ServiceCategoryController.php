<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    /**
     * Affiche toutes les catégories de services avec filtrage par type.
     */
    public function index(Request $request)
    {
        // Only prestations (event services) are supported
        $kind = ServiceCategory::KIND_PRESTATIONS;
        $query = ServiceCategory::query()->with(['providers' => function($q) {
            $q->with(['city', 'category'])->where('is_verified', true)->latest();
        }])->withCount(['providers' => function($q) {
            $q->where('is_verified', true);
        }])->where('kind', $kind);

        $categories = $query->get();

        return view('categories.index', compact('categories', 'kind'));
    }

    /**
     * Affiche une catégorie spécifique et la liste de ses prestataires.
     */
    public function show(ServiceCategory $category)
    {
        // On charge les prestataires associés via la relation Many-to-Many
        $providers = $category->providers()->with(['city', 'categories'])->paginate(12);

        return view('categories.show', compact('category', 'providers'));
    }
}