<?php

namespace App\Models;

use App\Support\LocationCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'cities';
    protected $fillable = ['name', 'state_id', 'status'];

    protected static function booted(): void
    {
        static::saved(fn () => LocationCache::bust());
        static::deleted(fn () => LocationCache::bust());
    }

    function state(){
        return $this->belongsTo(State::class, 'state_id');
    }
}
