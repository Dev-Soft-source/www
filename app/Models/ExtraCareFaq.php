<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtraCareFaq extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function extraCareFaqDetail(): HasMany
    {
        return $this->hasMany(ExtraCareFaqDetail::class);
    }
}
