<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignedService extends Model
{
    protected $fillable = [
        'service_request_id',
        'provider_id',
        'admin_id',
        'status',
        'rejection_reason',
        'assigned_at',
        'responded_at',
        'completed_at',
        'viewed_at', // Ajouté pour gestion du badge Nouveau
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
        'viewed_at' => 'datetime', // Ajouté
    ];

    protected static function booted(): void
    {
        // When status changes, notify the client
        static::updated(function (AssignedService $model) {
            // When status changes to 'accepted', notify the client and create a booking
            if ($model->isDirty('status') && $model->status === 'accepted' && $model->getOriginal('status') === 'pending') {
                $client = $model->serviceRequest->user;
                // Notify client that provider accepted and they can now discuss/start
                $client->notify(new \App\Notifications\AssignmentAcceptedNotification($model));
                $model->serviceRequest->update(['status' => 'assigned']);
                
                // Create or update booking - no more payment before service
                $booking = \App\Models\Booking::updateOrCreate(
                    ['assigned_service_id' => $model->id],
                    [
                        'user_id' => $model->serviceRequest->user_id,
                        'provider_id' => $model->provider_id,
                        'service_request_id' => $model->service_request_id,
                        'event_date' => $model->serviceRequest->event_date,
                        'event_details' => $model->serviceRequest->description,
                        'total_price' => $model->serviceRequest->budget,
                        'status' => 'confirmed',
                        'payment_status' => 'not_applicable', // Payment is direct between client and provider
                    ]
                );

                // Notify Admin too
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AssignmentAcceptedAdminNotification($model));
                }
            }
            
            // When status changes to 'rejected', notify the client and admin
            if ($model->isDirty('status') && $model->status === 'rejected' && $model->getOriginal('status') === 'pending') {
                $client = $model->serviceRequest->user;
                $client->notify(new \App\Notifications\AssignmentRejectedNotification($model));
                
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AssignmentRejectedAdminNotification($model));
                }
            }
            
            // When status changes to 'completed', notify admin and client
            if ($model->isDirty('status') && $model->status === 'completed' && $model->getOriginal('status') === 'accepted') {
                // Notify client to review
                $client = $model->serviceRequest->user;
                $client->notify(new \App\Notifications\AssignmentCompletionClientNotification($model));

                // Mark associated booking as done by provider
                $booking = \App\Models\Booking::where('assigned_service_id', $model->id)->first();
                if ($booking) {
                    $booking->update([
                        'provider_done' => true,
                        'provider_done_at' => now(),
                    ]);
                }

                // Notify admin
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AssignmentCompletionAdminNotification($model));
                }
            }
        });

        // When a new assignment is created, notify the provider and update the service request
        static::created(function (AssignedService $model) {
            $model->serviceRequest->update(['status' => 'assigned']);
            
            // Notify provider
            $providerUser = $model->provider->user;
            if ($providerUser) {
                $providerUser->notify(new \App\Notifications\ServiceAssignedNotification($model));
            }

            // Notify client that we are looking for their service
            $client = $model->serviceRequest->user;
            $client->notify(new \App\Notifications\ServiceRequestAssignedToProviderNotification($model));
        });
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function client()
    {
        return $this->serviceRequest->user();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}


