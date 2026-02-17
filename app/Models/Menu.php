<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_top_menu',
    ];

    protected $casts = [
        'is_top_menu' => 'boolean',
    ];

    public function menuDetails()
    {
        return $this->hasMany(MenuDetail::class);
    }
}
