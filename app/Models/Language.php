<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
        'native_name',
        'is_default',
        'direction',
        'flag_icon',
    ];


    public function getFlagIconAttribute($value)
    {
        if ($value) {
            return rtrim(config('app.url'), '/') . '/api/app/v1/flag-icons/' . rawurlencode($value);
        }

        return null;
    }

    public static function resolveLanguage($abbr = null)
    {
        return self::when($abbr, function ($query) use ($abbr) {
            $query->where('abbreviation', $abbr);
        })
            ->first()
            ?? self::where('is_default', 1)->first();
    }
}
