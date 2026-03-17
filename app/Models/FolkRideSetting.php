<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolkRideSetting extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'average_rating',
        'driver_age',
        'verfiy_phone',
        'verify_email',
        'driver_license',
        'extra_rides_trip_limit',
    ];

    public function requiresVerifiedPhone(): bool
    {
        return (string) $this->verfiy_phone === '1';
    }

    public function requiresVerifiedEmail(): bool
    {
        return (string) $this->verify_email === '1';
    }

    public function requiresDriverLicense(): bool
    {
        return (string) $this->driver_license === '1';
    }
}
