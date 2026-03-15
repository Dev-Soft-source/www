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

    function messages()
    {
        return $this->hasMany(Message::class);
    }

    function rides()
    {
        return $this->hasMany(Ride::class, 'added_by');
    }

    function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'user_id');
    }

    function phoneNumbers()
    {
        return $this->hasMany(PhoneNumber::class, 'user_id');
    }

    public function hasPhone()
    {
        return $this->phoneNumbers()->exists();
    }

    public function hasVerifiedPhone()
    {
        return $this->phoneNumbers()
            ->where('verified', 1)
            ->exists();
    }

    public function canUsePinkRide(?PinkRideSetting $pinkRideSetting = null): bool
    {
        if ((string) $this->pink_ride === '0') {
            return false;
        }

        if ((string) $this->pink_ride === '1') {
            return true;
        }

        $pinkRideSetting = $pinkRideSetting ?: PinkRideSetting::first();

        if (!$pinkRideSetting) {
            return true;
        }

        if (strtolower((string) $this->gender) !== 'female') {
            return false;
        }

        if (empty($this->government_issued_id) || empty($this->address)) {
            return false;
        }

        if ($pinkRideSetting->verfiy_phone === '1' && !$this->hasPinkRideVerifiedPhone()) {
            return false;
        }

        if ($pinkRideSetting->verify_email === '1' && (string) $this->email_verified !== '1') {
            return false;
        }

        if ($pinkRideSetting->driver_license === '1' && (string) $this->driver !== '1') {
            return false;
        }

        return true;
    }

    public function canUseExtraRide(
        ?FolkRideSetting $folkRideSetting = null,
        ?float $overallRating = null,
        ?int $totalRides = null,
        ?int $noShowsCount = null,
        ?int $cancellationCount = null,
        ?int $noshows = null
    ): bool {
        if ((string) $this->folks_ride === '0') {
            return false;
        }

        if ((string) $this->folks_ride === '1') {
            return true;
        }

        $folkRideSetting = $folkRideSetting ?: FolkRideSetting::first();

        if (!$folkRideSetting) {
            return true;
        }

        $overallRating = $overallRating ?? $this->driverOverallRating();
        $totalRides = $totalRides ?? $this->completedRideCount();
        $noShowsCount = $noShowsCount ?? $this->recentDriverNoShowsCount();
        $cancellationCount = $cancellationCount ?? $this->recentDriverCancellationCount();
        $noshows = $noshows ?? $this->recentAnyDriverNoShowsCount();
        $age = $this->age();

        if ($folkRideSetting->verfiy_phone === '1' && !$this->hasVerifiedPhone()) {
            return false;
        }

        if ($folkRideSetting->verify_email === '1' && (string) $this->email_verified !== '1') {
            return false;
        }

        if ($folkRideSetting->driver_license === '1' && (string) $this->driver !== '1') {
            return false;
        }

        if (empty($this->government_issued_id) || empty($this->address)) {
            return false;
        }

        if ($overallRating < (float) ($folkRideSetting->average_rating ?? 0)) {
            return false;
        }

        if ($age < (int) ($folkRideSetting->driver_age ?? 0)) {
            return false;
        }

        if ($totalRides < (int) ($folkRideSetting->extra_rides_trip_limit ?? 0)) {
            return false;
        }

        if ($noShowsCount > 0 || $cancellationCount > 0 || $noshows > 0) {
            return false;
        }

        return true;
    }

    public function pinkRideTooltip(?PostRidePageSettingDetail $postRidePage = null, ?PinkRideSetting $pinkRideSetting = null): string
    {
        $postRidePage = $postRidePage ?: $this->currentPostRidePage();

        if ((string) $this->pink_ride === '0') {
            return $postRidePage->pink_ride_tooltip_admin_disable_text ?? '';
        }

        if ((string) $this->pink_ride === '1') {
            return $postRidePage->pink_ride_tooltip_admin_enable_text ?? '';
        }

        $pinkRideSetting = $pinkRideSetting ?: PinkRideSetting::first();

        if (!$pinkRideSetting) {
            return '';
        }

        $parts = [
            $postRidePage->pink_ride_tooltip_only_text ?? '',
            $postRidePage->pink_ride_tooltip_female_text ?? '',
            $postRidePage->pink_ride_tooltip_driver_text ?? '',
        ];

        if ($pinkRideSetting->profile_complete === '1') {
            $parts[] = $postRidePage->pink_ride_tooltip_complete_profile_text ?? '';
        }

        if ($pinkRideSetting->verfiy_phone === '1' && !$this->hasPinkRideVerifiedPhone()) {
            $parts[] = $postRidePage->pink_ride_tooltip_phone_number_text ?? '';
        }

        if ($pinkRideSetting->verify_email === '1' && (string) $this->email_verified !== '1') {
            $parts[] = $postRidePage->pink_ride_tooltip_email_text ?? '';
        }

        if ($pinkRideSetting->driver_license === '1' && (string) $this->driver !== '1') {
            $parts[] = $postRidePage->pink_ride_tooltip_driver_license_text ?? '';
        }

        $parts[] = $postRidePage->pink_ride_tooltip_verified_text ?? '';
        $parts[] = $postRidePage->pink_ride_tooltip_select_this_ride_text ?? '';

        return trim(implode(' ', array_filter($parts)));
    }

    public function extraRideTooltip(
        ?PostRidePageSettingDetail $postRidePage = null,
        ?FolkRideSetting $folkRideSetting = null,
        ?float $overallRating = null,
        ?int $totalRides = null,
        ?int $noShowsCount = null,
        ?int $cancellationCount = null,
        ?int $noshows = null
    ): string {
        $postRidePage = $postRidePage ?: $this->currentPostRidePage();

        if ((string) $this->folks_ride === '0') {
            return $postRidePage->extra_care_tooltip_admin_disable_text ?? '';
        }

        if ((string) $this->folks_ride === '1') {
            return $postRidePage->extra_care_tooltip_admin_enable_text ?? '';
        }

        $folkRideSetting = $folkRideSetting ?: FolkRideSetting::first();

        if (!$folkRideSetting) {
            return '';
        }

        $parts = [
            $postRidePage->extra_care_tooltip_driver_review_text ?? '',
            $folkRideSetting->average_rating ?? '0',
            $postRidePage->extra_care_tooltip_greater_age_text ?? '',
            $folkRideSetting->driver_age ?? '0',
            $postRidePage->extra_care_tooltip_greater_text ?? '',
        ];

        if ($folkRideSetting->profile_complete === '1') {
            $parts[] = $postRidePage->extra_care_tooltip_complete_profile_text ?? '';
        }

        if ($folkRideSetting->verfiy_phone === '1' && !$this->hasVerifiedPhone()) {
            $parts[] = $postRidePage->extra_care_tooltip_phone_number_text ?? '';
        }

        if ($folkRideSetting->verify_email === '1' && (string) $this->email_verified !== '1') {
            $parts[] = $postRidePage->extra_care_tooltip_email_text ?? '';
        }

        if ($folkRideSetting->driver_license === '1' && (string) $this->driver !== '1') {
            $parts[] = $postRidePage->extra_care_tooltip_driver_license_text ?? '';
        }

        if (
            $folkRideSetting->verfiy_phone === '1' ||
            $folkRideSetting->verify_email === '1' ||
            $folkRideSetting->driver_license === '1'
        ) {
            $parts[] = $postRidePage->extra_care_tooltip_verified_text ?? '';
        }

        $parts[] = $postRidePage->extra_care_tooltip_eligible_text ?? '';

        return trim(implode(' ', array_filter($parts)));
    }

    public function primaryPhone()
    {
        return $this->phoneNumbers()
            ->where('default', 1)
            ->first();
    }

    function driver_payout()
    {
        return $this->hasMany(Payout::class, 'user_id');
    }

    function bankDetail()
    {
        return $this->hasOne(BankDetail::class, 'user_id');
    }

    function seatDetail()
    {
        return $this->hasMany(SeatDetail::class, 'user_id');
    }

    public function isBlockedBooking()
    {
        return $this->block_booking;
    }

    protected function hasPinkRideVerifiedPhone(): bool
    {
        return $this->hasVerifiedPhone() || (string) ($this->phone_verified ?? '') === '1';
    }

    protected function age(): int
    {
        if (empty($this->dob)) {
            return 0;
        }

        return \Carbon\Carbon::parse($this->dob)->diffInYears(now());
    }

    protected function driverOverallRating(): float
    {
        return (float) Rating::where('type', 1)
            ->where('status', 1)
            ->whereHas('ride', function ($query) {
                $query->where('added_by', $this->id);
            })
            ->avg('average_rating');
    }

    protected function completedRideCount(): int
    {
        return Ride::where('added_by', $this->id)
            ->where('status', '!=', 2)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereDate('completed_date', '<', now()->toDateString())
                        ->orWhere(function ($query) {
                            $query->whereDate('completed_date', '=', now()->toDateString())
                                ->whereTime('completed_time', '<', now()->toTimeString());
                        });
                });
            })
            ->count();
    }

    protected function recentDriverNoShowsCount(): int
    {
        return NoShowHistory::where('user_id', $this->id)
            ->where('type', 'driver')
            ->whereBetween('created_at', [now()->subMonths(3), now()])
            ->count();
    }

    protected function recentDriverCancellationCount(): int
    {
        return CancellationHistory::where('user_id', $this->id)
            ->where('type', 'driver')
            ->whereBetween('created_at', [now()->subMonths(3), now()])
            ->whereNotNull('booking_id')
            ->count();
    }

    protected function recentAnyDriverNoShowsCount(): int
    {
        return NoShowHistory::where('user_id', $this->id)
            ->where('type', 'driver')
            ->whereBetween('created_at', [now()->subMonths(3), now()])
            ->count();
    }

    protected function currentPostRidePage(): ?PostRidePageSettingDetail
    {
        $selectedLanguage = Language::resolveLanguage(session('selectedLanguage'));
        $defaultLanguageId = Language::where('is_default', 1)->value('id') ?? 1;

        return PostRidePageSettingDetail::getByLanguageWithFallback(
            $selectedLanguage?->id ?? $defaultLanguageId,
            $defaultLanguageId
        );
    }
}
