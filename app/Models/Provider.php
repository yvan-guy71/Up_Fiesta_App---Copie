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
        'cni_number',
        'years_of_experience',
        'cni_photo_front',
        'cni_photo_back',
        'is_company',
        'company_registration_number',
        'company_proof_doc_front',
        'company_proof_doc_back',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected $casts = [
        'is_verified' => 'boolean',
        'is_company' => 'boolean',
        'base_price' => 'decimal:2',
        'price_range_max' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'category_provider');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProviderMedia::class)->orderBy('sort_order');
    }

    /**
     * Check if provider is approved
     */
    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if provider is rejected
     */
    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Check if provider is pending verification
     */
    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }
}

