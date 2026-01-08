<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BankDetail extends Model
{
    use HasFactory;
    
    public $table = "bank_details";

    protected $guarded = [];

    function bank(){
        return $this->belongsTo(Bank::class, 'bank_id');
    }
    protected static function booted()
    {
        parent::booted();

        static::saved(function ($bank) {
            if (!isset($bank->random_id)) {
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $bank->random_id = $randomStr . '-' . $bank->id;
                $bank->save();
            }
        });
    }
}
