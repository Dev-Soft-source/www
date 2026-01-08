<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMessageCount extends Model
{
    use HasFactory;
    public $table = "user_message_count";
    protected $guarded = [];

}
