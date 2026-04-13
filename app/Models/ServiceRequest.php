<?php

namespace App\Models;

use App\Models\Provider;
use App\Models\User;
use App\Models\Event as EventModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    // Only prestations (event services) are supported now
    public const KIND_PRESTATIONS = 'prestations';

    protected $fillable = [
        'user_id',
        'type',
        'kind',
        'provider_id',
        'event_id',
        'subject',
        'description',
        'status',
        'event_date',
        'location',
        'budget',
        'viewed_at',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'budget' => 'decimal:2',
        'viewed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updated(function ($record) {
            if ($record->isDirty('provider_id') && $record->provider_id) {
                $provider = $record->provider;
                if ($provider && $provider->user) {
                    $provider->user->notify(new \App\Notifications\ServiceRequestDirectNotification($record));
                    
                    // Also send SMS if possible
                    try {
                        $message = "Upfiesta: Vous avez une nouvelle demande de service assignée. Connectez-vous pour la consulter.";
                        if ($provider->phone) {
                            \App\Services\SmsService::send($provider->phone, $message);
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Erreur SMS assignation: " . $e->getMessage());
                    }
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function event()
    {
        return $this->belongsTo(EventModel::class);
    }

    public function assignedServices(): HasMany
    {
        return $this->hasMany(AssignedService::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}



