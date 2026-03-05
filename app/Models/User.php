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

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'user_id');
    }

    public function getProfileImageAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // Check if the value is a URL
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value; // Return the URL as is
            }
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/api/app/v1/users-images/' . rawurlencode($value);
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
            return rtrim(config('app.url'), '/') . '/api/app/v1/users-images/' . rawurlencode($value);
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
            return rtrim(config('app.url'), '/') . '/api/app/v1/driver-liscenses/' . rawurlencode($value);
        }
        
        return null;
    }

    public function getDriverLicenseOriginalUploadAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/api/app/v1/driver-liscenses/' . rawurlencode($value);
        }
        
        return null;
    }

    public function getStudentCardAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/api/app/v1/student-cards/' . rawurlencode($value);
        }
        
        return null;
    }

    public function getStudentCardOriginalUploadAttribute($value)
    {
        // You can perform any transformation you need here
        if ($value) {
            // For example, prepend the base URL to the image path
            return rtrim(config('app.url'), '/') . '/api/app/v1/student-cards/' . rawurlencode($value);
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

    /**
     * Determine if Pink Ride features should be disabled for this user,
     * based on the same logic currently used in Blade.
     */
    public function isPinkRideDisabled(?\App\Models\PinkRideSetting $pinkRideSetting = null): bool
    {
        // Explicitly disabled by admin flag
        if ($this->pink_ride === '0') {
            return true;
        }

        // If pink_ride has a non-empty, non-zero value (e.g. '1'), treat as override
        if ($this->pink_ride !== null && $this->pink_ride !== '') {
            return false;
        }

        // When pink_ride is empty, apply PinkRideSetting rules (if any)
        if (!$pinkRideSetting) {
            // No settings to enforce → do not disable here
            return false;
        }

        // Gender and identity checks
        if ($this->gender === 'female' && (empty($this->government_issued_id) || empty($this->address))) {
            return true;
        }

        if ($this->gender !== 'female') {
            return true;
        }

        // Phone verification requirement
        if ($pinkRideSetting->verfiy_phone === '1' && $this->phone_verified !== '1') {
            return true;
        }

        // Email verification requirement
        if ($pinkRideSetting->verify_email === '1' && $this->email_verified !== '1') {
            return true;
        }

        // Driver licence requirement
        if ($pinkRideSetting->driver_license === '1' && $this->driver !== '1') {
            return true;
        }

        return false;
    }

    /**
     * Determine if Extra+ (Folk) Ride features should be disabled for this user,
     * based on the legacy Blade logic.
     *
     * @param  \App\Models\FolkRideSetting|null  $folkSetting
     * @param  float|null  $overallRating
     * @param  int|null    $age
     * @param  int|null    $totalRidesCount
     * @param  int|null    $rideLimit
     * @param  int|null    $noShowsCount
     * @param  int|null    $cancellationCount
     * @param  int|null    $noShows
     */
    public function isFolkRideDisabled(
        ?\App\Models\FolkRideSetting $folkSetting = null,
        ?float $overallRating = null,
        ?int $age = null,
        ?int $totalRidesCount = null,
        ?int $rideLimit = null,
        ?int $noShowsCount = null,
        ?int $cancellationCount = null,
        ?int $noShows = null
    ): bool {
        // Explicitly disabled by admin flag
        if ($this->folks_ride === '0') {
            return true;
        }

        // If folks_ride has a non-empty, non-zero value (e.g. '1'), treat as override
        if ($this->folks_ride !== null && $this->folks_ride !== '') {
            return false;
        }

        // When folks_ride is empty, apply FolkRideSetting rules (if any)
        if (!$folkSetting) {
            return false;
        }

        // Phone verification requirement (for passengers: check any verified phone)
        if ($folkSetting->verfiy_phone === '1') {
            $hasVerifiedPhone = $this->phone_numbers && $this->phone_numbers->contains('verified', 1);
            if (!$hasVerifiedPhone) {
                return true;
            }
        }

        // Email verification requirement
        if ($folkSetting->verify_email === '1' && $this->email_verified !== '1') {
            return true;
        }

        // Driver licence requirement
        if ($folkSetting->driver_license === '1' && $this->driver !== '1') {
            return true;
        }

        // Rating, age, ride count, no-shows, cancellations, additional noshows flag
        $failsRatingOrAge = false;
        if ($overallRating !== null && $folkSetting->average_rating !== null && $age !== null && $folkSetting->driver_age !== null) {
            $failsRatingOrAge = ($overallRating < $folkSetting->average_rating) || ($age < $folkSetting->driver_age);
        }

        $failsRideCount = false;
        if ($totalRidesCount !== null && $rideLimit !== null) {
            $failsRideCount = $totalRidesCount < $rideLimit;
        }

        $failsNoShows = ($noShowsCount !== null && $noShowsCount > 0) || ($noShows !== null && $noShows > 0);
        $failsCancellations = $cancellationCount !== null && $cancellationCount > 0;

        if ($failsRatingOrAge || $failsRideCount || $failsNoShows || $failsCancellations) {
            return true;
        }

        // Identity: government ID and address must be present
        if (empty($this->government_issued_id) || empty($this->address)) {
            return true;
        }

        return false;
    }
}

