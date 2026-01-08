<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewSetting extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'leave_review_days',
        'respond_review_days',
    ];
}
