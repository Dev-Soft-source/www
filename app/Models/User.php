<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    public function getProfileImageAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // Check if the value is a URL
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value; // Return the URL as is
            }
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/users_images/' . $value;
        } elseif ($this->gender === 'male' || $this->gender === 'Male') {
            return rtrim(config('app.url'), '/') . '/assets/male.png';
        } elseif ($this->gender === 'female' || $this->gender === 'Female') {
            return rtrim(config('app.url'), '/') . '/assets/female.png';
        } elseif ($this->gender === 'prefer not to say' || $this->gender === 'Prefer not to say') {
            return rtrim(config('app.url'), '/') . '/assets/neutral.png';
        }
        
        return null;
    }

    public function getProfileOriginalImageAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // Check if the value is a URL
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value; // Return the URL as is
            }
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/users_images/' . $value;
        } elseif ($this->gender === 'male' || $this->gender === 'Male') {
            return rtrim(config('app.url'), '/') . '/assets/male.png';
        } elseif ($this->gender === 'female' || $this->gender === 'Female') {
            return rtrim(config('app.url'), '/') . '/assets/female.png';
        } elseif ($this->gender === 'prefer not to say' || $this->gender === 'Prefer not to say') {
            return rtrim(config('app.url'), '/') . '/assets/neutral.png';
        }
        
        return null;
    }
    
    public function getDriverLiscenseAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/driver_liscenses/' . $value;
        }
        
        return null;
    }

    public function getDriverLicenseOriginalUploadAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/driver_liscenses/' . $value;
        }
        
        return null;
    }

    public function getStudentCardAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/student_cards/' . $value;
        }
        
        return null;
    }

    public function getStudentCardOriginalUploadAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/student_cards/' . $value;
        }
        
        return null;
    }

    public function getGovernmentIssuedIdAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/users_government_ids/' . $value;
        }
        
        return null;
    }

    public function getGovernmentIssuedOriginalIdAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/users_government_ids/' . $value;
        }
        
        return null;
    }

    public function getReferralUuidAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/en/signup-with-referral/' . $value;
        }
        
        return null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function messages(){
        return $this->hasMany(Message::class);
    }

    function rides(){
        return $this->hasMany(Ride::class, 'added_by');
    }

    function bookings(){
        return $this->hasMany(Booking::class, 'user_id');
    }

    function vehicles(){
        return $this->hasMany(Vehicle::class, 'user_id');
    }

    function phone_numbers(){
        return $this->hasMany(PhoneNumber::class, 'user_id');
    }

    function driver_payout(){
        return $this->hasMany(Payout::class, 'user_id');
    }

    function bankDetail(){
        return $this->hasOne(BankDetail::class, 'user_id');
    }

    function seatDetail(){
        return $this->hasMany(SeatDetail::class, 'user_id');
    }

    function phoneNumber(){
        return $this->hasMany(PhoneNumber::class, 'user_id');
    }
}
