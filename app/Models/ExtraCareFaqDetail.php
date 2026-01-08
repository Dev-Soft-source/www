<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraCareFaqDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function extraCareFaq(): BelongsTo
    {
        return $this->belongsTo(ExtraCareFaq::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
