<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // When status changes, notify the client
        static::updated(function (AssignedService $model) {
            // When status changes to 'accepted', notify the client
            if ($model->isDirty('status') && $model->status === 'accepted' && $model->getOriginal('status') === 'pending') {
                $client = $model->serviceRequest->user;
                $client->notify(new \App\Notifications\AssignmentAcceptedNotification($model));
                $model->serviceRequest->update(['status' => 'assigned']);
            }
            
            // When status changes to 'rejected', notify the client
            if ($model->isDirty('status') && $model->status === 'rejected' && $model->getOriginal('status') === 'pending') {
                $client = $model->serviceRequest->user;
                $client->notify(new \App\Notifications\AssignmentRejectedNotification($model));
            }
            
            // When status changes to 'completed', notify admin
            if ($model->isDirty('status') && $model->status === 'completed' && $model->getOriginal('status') === 'accepted') {
                // Notify admin
                if ($model->admin) {
                    $model->admin->notify(new \App\Notifications\AssignmentCompletionAdminNotification($model));
                }
            }
        });

        // When a new assignment is created, notify the client and update the service request
        static::created(function (AssignedService $model) {
            $model->serviceRequest->update(['status' => 'assigned']);
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


