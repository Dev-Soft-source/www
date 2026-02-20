<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteText extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'text',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(SiteTextDetail::class, 'slug_id');
    }
}
