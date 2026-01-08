<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RideDetail extends Model
{
    use HasFactory;

    public $table = "ride_details";
    protected $guarded = [];

    function ride()
    {
        return $this->belongsTo(Ride::class, 'ride_id', 'id');
    }

    protected static function booted()
    {
        static::updated(function ($rideDetail) {
            $changes = [];
            // dd($rideDetail);

            
            $dirtyData = $rideDetail->getDirty();
            unset($dirtyData['created_at'], $dirtyData['updated_at']);

            if (!empty($dirtyData)) {
                // foreach ($oldRideData as $field => $newValue) {
                //     $oldValue = $ride->getOriginal($field);

                //     if($newValue != $oldValue){
                //         $changes[] = [
                //             $field => $newValue,                        
                //         ];
                //     }
                // }
                $originalPrice = $rideDetail->getOriginal('price');
        $newPrice = $rideDetail->price;
        
        if ($rideDetail->isDirty()) {
                    if ($originalPrice !== $newPrice) {
                    DB::table('post_ride_logs')->insert([
                        'ride_id' => $rideDetail->ride_id,
                        'action' => 'updated',
                        'changes' => json_encode($dirtyData),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                }
            }
        });
    }
}
