<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function PackageDetail(): HasMany
    {
        return $this->hasMany(PackageDetail::class,);
    }
}
