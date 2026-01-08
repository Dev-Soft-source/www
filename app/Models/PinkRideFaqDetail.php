<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PinkRideFaqDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pinkRideFaq(): BelongsTo
    {
        return $this->belongsTo(PinkRideFaq::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
