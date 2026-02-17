<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_icon',
        'insta_icon',
        'youtube_icon',
        'twitter_icon',
        'menu1',
        'menu2',
        'menu3',
        'menu4',
    ];

    public function menu1Relation()
    {
        return $this->belongsTo(Menu::class, 'menu1');
    }

    public function menu2Relation()
    {
        return $this->belongsTo(Menu::class, 'menu2');
    }

    public function menu3Relation()
    {
        return $this->belongsTo(Menu::class, 'menu3');
    }

    public function menu4Relation()
    {
        return $this->belongsTo(Menu::class, 'menu4');
    }
}
