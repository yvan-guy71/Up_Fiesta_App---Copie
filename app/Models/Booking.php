<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'service_request_id',
        'assigned_service_id',
        'event_date',
        'event_details',
        'total_price',
        'commission_rate',
        'platform_fee',
        'provider_amount',
        'status',
        'payment_status',
        'transaction_id',
        'payment_method',
        'payout_status',
        'payout_date',
        'provider_done',
        'provider_done_at',
        'require_client_review',
        'client_review_requested_at',
        'admin_verification_status',
        'admin_verified_at',
        'admin_verified_by',
        'provider_commission_reduction',
    ];

    protected $casts = [
        'event_date' => 'date',
        'payout_date' => 'datetime',
        'provider_done' => 'boolean',
        'provider_done_at' => 'datetime',
        'require_client_review' => 'boolean',
        'client_review_requested_at' => 'datetime',
        'admin_verified_at' => 'datetime',
        'provider_commission_reduction' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function assignedService(): BelongsTo
    {
        return $this->belongsTo(AssignedService::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_verified_by');
    }

    protected static function booted(): void
    {
        // Quand le prestataire marque le service comme terminé, demander au client de noter
        static::updated(function (Booking $booking) {
            if ($booking->isDirty('provider_done') && $booking->provider_done && !$booking->getOriginal('provider_done')) {
                $reviewService = app(\App\Services\BookingReviewService::class);
                $reviewService->requestClientReview($booking);
            }
        });
    }
}
