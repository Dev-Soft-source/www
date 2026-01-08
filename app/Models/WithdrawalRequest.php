<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['user_id','amount','method','account_no','bank_name','ifsc_code','country','paypal_email'];

    function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
