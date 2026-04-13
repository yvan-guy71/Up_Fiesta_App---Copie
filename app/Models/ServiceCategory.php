<?php

namespace App\Models;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    // types of categories for organizational purposes
    public const KIND_PRESTATIONS = 'prestations';

    public static array $kinds = [
        self::KIND_PRESTATIONS,
    ];

    protected $fillable = [
        'name',
        'kind',
    ];

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(Provider::class, 'category_provider');
    }

    public function singleProviders(): HasMany
    {
        return $this->hasMany(Provider::class, 'category_id');
    }
}
