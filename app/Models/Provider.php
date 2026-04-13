<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'category_id',
        'city_id',
        'description',
        'email',
        'website',
        'logo',
        'is_verified',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'base_price',
        'price_range_max',
        'pending_base_price',
        'pending_price_range_max',
        'price_change_status', // 'none', 'pending', 'rejected'
        'cni_number',
        'years_of_experience',
        'cni_photo_front',
        'cni_photo_back',
        'is_company',
        'company_registration_number',
        'company_proof_doc_front',
        'company_proof_doc_back',
    ];

    // On ajoute 'logo_url' à l'export JSON automatiquement
    protected $appends = ['logo_url'];

    /**
     * Accesseur pour obtenir l'URL complète du logo
     */
    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return null;
        }

        // Si le logo est déjà une URL complète
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        // Nettoyage du chemin
        $path = ltrim($this->logo, '/');

        // S'assurer que le chemin contient 'storage/'
        if (!str_starts_with($path, 'storage/')) {
            $path = 'storage/' . $path;
        }

        // Forcer l'utilisation de APP_URL défini dans le .env pour éviter localhost
        $baseUrl = rtrim(env('APP_URL', 'https://upfiesta.com'), '/');

        return $baseUrl . '/' . $path;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'is_verified' => 'boolean',
        'is_mentor' => 'boolean',
        'is_company' => 'boolean',
        'base_price' => 'decimal:2',
        'price_range_max' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'category_provider', 'provider_id', 'service_category_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProviderMedia::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_provider');
    }
}
