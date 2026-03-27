<?php

namespace App\Models;

use App\Support\LocationCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = ['name','country_id', 'ride_limit', 'tax'];

    protected static function booted(): void
    {
        static::saved(fn () => LocationCache::bust());
        static::deleted(fn () => LocationCache::bust());
    }

    function country(){
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function cities()
{
    return $this->hasMany(City::class);
}
}
