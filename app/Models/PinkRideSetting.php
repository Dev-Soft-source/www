<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinkRideSetting extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'female',
        'verfiy_phone_passenger',
        'verfiy_phone',
        'verify_email',
        'driver_license',
    ];

    public function requiresFemaleDriver(): bool
    {
        return (string) $this->female === '1';
    }

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
