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
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/flag_icons/' . $value;
        }
        
        return null;
    }

}
