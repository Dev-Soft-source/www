<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentSearch extends Model
{
    use HasFactory;

    protected $fillable = ['from', 'to', 'from_city_id', 'to_city_id', 'user_id', 'page_type'];
}
