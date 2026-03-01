<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PxRideSearchLog extends Model
{
    protected $table = 'px_ride_search_logs';

    protected $fillable = [
        'user_id',
        'origin_city_id',
        'destination_city_id',
        'origin_label',
        'destination_label',
        'departure_date',
        'seats_required',
        'results_count',
        'filters',
        'ip_address',
        'user_agent',
        'searched_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'departure_date' => 'date',
        'searched_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

