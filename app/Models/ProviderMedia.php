<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderMedia extends Model
{
    protected $fillable = [
        'provider_id',
        'type',
        'file_path',
        'title',
        'sort_order',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
