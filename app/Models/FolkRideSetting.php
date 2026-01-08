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
    ];
}
