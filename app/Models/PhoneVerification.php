<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PhoneVerification extends Model
{
    use HasFactory;

    function phone(){
        return $this->belongsTo(PhoneNumber::class, 'phone_number_id');
    }
    protected static function booted()
    {
        parent::booted();

        static::saved(function ($PhoneVerification) {
            if (!isset($PhoneVerification->random_id)) {
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $PhoneVerification->random_id = $randomStr . '-' . $PhoneVerification->id;
                $PhoneVerification->save();
            }
        });
    }
}
