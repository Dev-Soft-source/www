<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClaimReward extends Model
{
    use HasFactory;
    protected static function booted()
    {
        parent::booted();

        static::saved(function ($claimReward) {
            if (!isset($claimReward->random_id)) {
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $claimReward->random_id = $randomStr . '-' . $claimReward->id;
                $claimReward->save();
            }
        });
    }
}
