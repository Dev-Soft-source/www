<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = ['random_id','departure','departure_lat','departure_lng','departure_place','departure_route','departure_zipcode','departure_city','departure_state','departure_state_short','departure_country',
        'destination','destination_lat','destination_lng','destination_place','destination_route','destination_zipcode','destination_city','destination_state','destination_state_short','destination_country',
        'total_distance','total_time','date','time','completed_date','completed_time','recurring','recurring_type','recurring_trips','recurring_id','details','seats','skip_vehicle','add_vehicle','added_vehicle','vehicle_id','make','model','vehicle_type','year','color','license_no','car_type','car_image','car_image_original','smoke','animal_friendly','features',
        'booking_method','booking_type','max_back_seats','luggage','accept_more_luggage','open_customized','price','payment_method','notes','added_by','until_date','until_limit','pickup','dropoff','middle_seats','back_seats',
        'status', 'added_on', 'remove_car_image'];

    public $timestamps = false;
    
    function driver(){
        return $this->belongsTo(User::class, 'added_by')->withTrashed();
    }

    function vehicle(){
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    function bookings(){
        return $this->hasMany(Booking::class, 'ride_id');
    }

    function rideDetail(){
        return $this->hasMany(RideDetail::class, 'ride_id', 'id');
    }

    function defaultRideDetail(){
        return $this->hasMany(RideDetail::class, 'ride_id', 'id')->where('default_ride', '1');
    }

    function MoreRideDetail(){
        return $this->hasMany(RideDetail::class, 'ride_id', 'id')->where('default_ride', '0');
    }

    function ratings(){
        return $this->hasMany(Rating::class, 'ride_id');
    }

    function payouts(){
        return $this->hasMany(Payout::class, 'ride_id');
    }

    public function postRideLogs()
    {
        return $this->hasMany(PostRideLog::class, 'ride_id');
    }

    public function pendingSeatDetail()
    {
        return $this->hasMany(SeatDetail::class, 'ride_id')->where('status', '!=', 'booked');
    }

    public function getCarImageAttribute($value)
    {
        // You can perform any transformation you need here
        if (isset($value) && $value != "") {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/car_images/' . $value;
        }elseif ($this->car_type === 'Convertable') {
            return rtrim(config('app.url'), '/') . '/assets/convertable.png';
        } elseif ($this->car_type === 'Hatchback') {
            return rtrim(config('app.url'), '/') . '/assets/Hatchback.png';
        } elseif ($this->car_type === 'Coupe') {
            return rtrim(config('app.url'), '/') . '/assets/Coupe.png';
        } elseif ($this->car_type === 'Minivan') {
            return rtrim(config('app.url'), '/') . '/assets/Minivan.png';
        } elseif ($this->car_type === 'Sedan') {
            return rtrim(config('app.url'), '/') . '/assets/Sedan.png';
        } elseif ($this->car_type === 'Station wagon') {
            return rtrim(config('app.url'), '/') . '/assets/Station Wagon.png';
        } elseif ($this->car_type === 'SUV') {
            return rtrim(config('app.url'), '/') . '/assets/SUV.png';
        } elseif ($this->car_type === 'Truck') {
            return rtrim(config('app.url'), '/') . '/assets/Truck.png';
        } elseif ($this->car_type === 'Van') {
            return rtrim(config('app.url'), '/') . '/assets/Van.png';
        }else{
            return rtrim(config('app.url'), '/') . '/assets/car.png';
        }
        
        return null;
    }

    public function getCarImageOriginalAttribute($value)
    {
        // You can perform any transformation you need here
        if (isset($value) && $value != "") {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/car_images/' . $value;
        }elseif ($this->car_type === 'Convertable') {
            return rtrim(config('app.url'), '/') . '/assets/convertable.png';
        } elseif ($this->car_type === 'Hatchback') {
            return rtrim(config('app.url'), '/') . '/assets/Hatchback.png';
        } elseif ($this->car_type === 'Coupe') {
            return rtrim(config('app.url'), '/') . '/assets/Coupe.png';
        } elseif ($this->car_type === 'Minivan') {
            return rtrim(config('app.url'), '/') . '/assets/Minivan.png';
        } elseif ($this->car_type === 'Sedan') {
            return rtrim(config('app.url'), '/') . '/assets/Sedan.png';
        } elseif ($this->car_type === 'Station wagon') {
            return rtrim(config('app.url'), '/') . '/assets/Station Wagon.png';
        } elseif ($this->car_type === 'SUV') {
            return rtrim(config('app.url'), '/') . '/assets/SUV.png';
        } elseif ($this->car_type === 'Truck') {
            return rtrim(config('app.url'), '/') . '/assets/Truck.png';
        } elseif ($this->car_type === 'Van') {
            return rtrim(config('app.url'), '/') . '/assets/Van.png';
        }else{
            return rtrim(config('app.url'), '/') . '/assets/car.png';
        }
        
        return null;
    }

    protected static function booted()
    {
        parent::booted();

        static::saved(function ($ride) {
            // dd($ride);
            if (!isset($ride->random_id)) {
                // dd($ride);
                $randomStr = strtoupper(Str::random(4)); // 4 random letters (A-Z)
                $ride->random_id = $randomStr . '-' . $ride->id;
                $ride->save();
            }
        });
    

        // static::created(function ($ride) {
           
        //     DB::table('post_ride_logs')->insert([
        //         'ride_id' => $ride->id,
        //         'action' => 'created',
        //         'changes' => json_encode($ride->getAttributes()),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // });

        static::updated(function ($ride) {

            $changes = [];

            $oldRideData = $ride->getDirty();
            if (!empty($oldRideData)) {
                // foreach ($oldRideData as $field => $newValue) {
                //     $oldValue = $ride->getOriginal($field);

                //     if($newValue != $oldValue){
                //         $changes[] = [
                //             $field => $newValue,                        
                //         ];
                //     }
                // }

                DB::table('post_ride_logs')->insert([
                    'ride_id' => $ride->id,
                    'action' => 'updated',
                    'changes' => json_encode($oldRideData),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
      
            }
        });

        static::deleted(function ($ride) {
            DB::table('post_ride_logs')->insert([
                'ride_id' => $ride->id,
                'action' => 'deleted',
                'changes' => json_encode($ride->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }


}
