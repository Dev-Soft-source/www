<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'placeholders',
    ];

    protected $casts = [
        'placeholders' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(NotificationMessageDetail::class);
    }
}
