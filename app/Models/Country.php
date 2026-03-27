<?php

namespace App\Models;

use App\Support\LocationCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';
    protected $fillable = ['name', 'dial_code', 'iso_code'];

    protected static function booted(): void
    {
        static::saved(fn () => LocationCache::bust());
        static::deleted(fn () => LocationCache::bust());
    }

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
