<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['user_id','make','model','type','liscense_no','color','year','car_type','image', 'original_image','remove_image','added_on','primary_vehicle'];

    public function getImageAttribute($value)
    {
        // You can perform any transformation you need here
        if (isset($value) && $value != "") {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/car_images/' . $value;
        } elseif ($this->vehicle_type === 'Convertable') {
            return rtrim(config('app.url'), '/') . '/assets/convertable.png';
        } elseif ($this->vehicle_type === 'Hatchback') {
            return rtrim(config('app.url'), '/') . '/assets/Hatchback.png';
        } elseif ($this->vehicle_type === 'Coupe') {
            return rtrim(config('app.url'), '/') . '/assets/Coupe.png';
        } elseif ($this->vehicle_type === 'Minivan') {
            return rtrim(config('app.url'), '/') . '/assets/Minivan.png';
        } elseif ($this->vehicle_type === 'Sedan') {
            return rtrim(config('app.url'), '/') . '/assets/Sedan.png';
        } elseif ($this->vehicle_type === 'Station wagon') {
            return rtrim(config('app.url'), '/') . '/assets/Station Wagon.png';
        } elseif ($this->vehicle_type === 'SUV') {
            return rtrim(config('app.url'), '/') . '/assets/SUV.png';
        } elseif ($this->vehicle_type === 'Truck') {
            return rtrim(config('app.url'), '/') . '/assets/Truck.png';
        } elseif ($this->vehicle_type === 'Van') {
            return rtrim(config('app.url'), '/') . '/assets/Van.png';
        }else{
            return rtrim(config('app.url'), '/') . '/assets/car.png';
        }
        
        return null;
    }

    public function getOriginalImageAttribute($value)
    {
        // You can perform any transformation you need here
        if (isset($value) && $value != "") {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/car_images/' . $value;
        } elseif ($this->vehicle_type === 'Convertable') {
            return rtrim(config('app.url'), '/') . '/assets/convertable.png';
        } elseif ($this->vehicle_type === 'Hatchback') {
            return rtrim(config('app.url'), '/') . '/assets/Hatchback.png';
        } elseif ($this->vehicle_type === 'Coupe') {
            return rtrim(config('app.url'), '/') . '/assets/Coupe.png';
        } elseif ($this->vehicle_type === 'Minivan') {
            return rtrim(config('app.url'), '/') . '/assets/Minivan.png';
        } elseif ($this->vehicle_type === 'Sedan') {
            return rtrim(config('app.url'), '/') . '/assets/Sedan.png';
        } elseif ($this->vehicle_type === 'Station wagon') {
            return rtrim(config('app.url'), '/') . '/assets/Station Wagon.png';
        } elseif ($this->vehicle_type === 'SUV') {
            return rtrim(config('app.url'), '/') . '/assets/SUV.png';
        } elseif ($this->vehicle_type === 'Truck') {
            return rtrim(config('app.url'), '/') . '/assets/Truck.png';
        } elseif ($this->vehicle_type === 'Van') {
            return rtrim(config('app.url'), '/') . '/assets/Van.png';
        }else{
            return rtrim(config('app.url'), '/') . '/assets/car.png';
        }
        
        return null;
    }
}
