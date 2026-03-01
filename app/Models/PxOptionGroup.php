<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PxOptionGroup extends Model
{
    protected $table = 'px_ride_option_groups';

    protected $fillable = [
        'code',
        'sort_order',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PxOption::class, 'group_id')->orderBy('sort_order');
    }
}
