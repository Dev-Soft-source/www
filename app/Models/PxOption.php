<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PxOption extends Model
{
    protected $table = 'px_ride_options';

    protected $fillable = [
        'group_id',
        'code',
        'is_active',
        'sort_order',
        'meta',
        'icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PxOptionGroup::class, 'group_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PxOptionTranslation::class, 'option_id');
    }

    public function rides(): BelongsToMany
    {
        return $this->belongsToMany(PxRide::class, 'px_ride_option_assignments', 'option_id', 'ride_id')
            ->withPivot('value')
            ->withTimestamps();
    }
}
