<?php

namespace App\Models;

use App\Models\Provider;
use App\Models\User;
use App\Models\Event as EventModel;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    // we record the service kind to distinguish prestations from domestiques
    public const KIND_PRESTATIONS = 'prestations';
    public const KIND_DOMESTIQUES = 'domestiques';

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
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'budget' => 'decimal:2',
    ];

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
}
