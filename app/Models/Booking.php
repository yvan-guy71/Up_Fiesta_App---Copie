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
    ];

    protected $casts = [
        'event_date' => 'date',
        'payout_date' => 'datetime',
        'provider_done' => 'boolean',
        'provider_done_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
