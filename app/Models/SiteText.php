<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteText extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'language_id',
        'text',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
