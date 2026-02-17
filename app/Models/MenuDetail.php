<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'menu_detail';

    protected $fillable = [
        'menu_id',
        'language_id',
        'section_title',
        'menu_items',
    ];

    protected $casts = [
        'menu_items' => 'array',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
