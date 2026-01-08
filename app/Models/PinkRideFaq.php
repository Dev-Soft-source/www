<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PinkRideFaq extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pinkRideFaqDetail(): HasMany
    {
        return $this->hasMany(PinkRideFaqDetail::class);
    }
}
