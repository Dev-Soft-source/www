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
}
