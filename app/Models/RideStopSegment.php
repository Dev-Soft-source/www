<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideStopSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'from_stop_id',
        'to_stop_id',
        'price_minor',
        'distance_meters',
        'duration_seconds',
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    public function fromStop()
    {
        return $this->belongsTo(RideStop::class, 'from_stop_id');
    }

    public function toStop()
    {
        return $this->belongsTo(RideStop::class, 'to_stop_id');
    }
}
