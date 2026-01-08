<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'agency',
        'image',
        'added_by',
        'added_on',
        'writer_image'
    ];

    public function articleDetail(): HasMany
    {
        return $this->hasMany(ArticleDetail::class,);
    }
}
