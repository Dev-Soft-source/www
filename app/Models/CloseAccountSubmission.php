<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloseAccountSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['name','reasons','recommend','improve_message','close_account_reason'];
}
