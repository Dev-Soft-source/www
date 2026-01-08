<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPackage extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'credits_buy',
        'credits_get',
        'credits_price',
        'added_on'
    ];
}
