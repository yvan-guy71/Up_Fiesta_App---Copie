<?php

namespace App\Observers;

use App\Models\ServiceRequest;
use App\Notifications\ServiceRequestDirectNotification;
use App\Notifications\ServiceRequestAcceptedByProviderNotification;
use App\Notifications\ServiceRequestRejectedByProviderNotification;

class ServiceRequestObserver
{
    /**
     * Handle the ServiceRequest "updated" event.
     */
    public function updated(ServiceRequest $serviceRequest): void
    {
        // When a provider is selected and status is still pending, notify the provider
        if ($serviceRequest->isDirty('provider_id') && $serviceRequest->provider_id && $serviceRequest->status === 'pending') {
            $provider = $serviceRequest->provider;
            if ($provider && $provider->user) {
                $provider->user->notify(new ServiceRequestDirectNotification($serviceRequest));
            }
        }

        // When provider accepts (status changes to assigned), notify client
        if ($serviceRequest->isDirty('status') && $serviceRequest->status === 'assigned' && $serviceRequest->getOriginal('status') === 'pending') {
            $client = $serviceRequest->user;
            if ($client) {
                $client->notify(new ServiceRequestAcceptedByProviderNotification($serviceRequest));
            }
        }

        // When provider is cleared (rejected) and status back to pending, notify client
        if ($serviceRequest->isDirty('provider_id') && !$serviceRequest->provider_id && $serviceRequest->getOriginal('provider_id')) {
            // Get the rejection reason if available (from a note or somewhere)
            $client = $serviceRequest->user;
            if ($client) {
                $client->notify(new ServiceRequestRejectedByProviderNotification($serviceRequest, ''));
            }
        }
    }

    /**
     * Handle the ServiceRequest "created" event.
     */
    public function created(ServiceRequest $serviceRequest): void
    {
        //
    }
}
