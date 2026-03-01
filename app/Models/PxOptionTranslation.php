<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PxOptionTranslation extends Model
{
    protected $table = 'px_ride_option_translations';

    protected $fillable = [
        'option_id',
        'language_id',
        'label',
        'description',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(PxOption::class, 'option_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
