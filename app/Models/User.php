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

    public function getProfileImageAttribute($value = null)
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

    public function scopeActive($query)
    {
        return $query->withoutTrashed();
    }

    public function scopeEmailNotificationsEnabled($query)
    {
        return $query->where('email_notification', 1);
    }

    public function scopeClosed($query)
    {
        return $query->where('closed', '1');
    }

    public function hasVerifiedPhone()
    {
        return $this->phoneNumbers()
            ->where('verified', 1)
            ->exists();
    }

    public function canUsePinkRide(?PinkRideSetting $pinkRideSetting = null): bool
    {
        // Explicitly disabled by admin flag.
        if ((string) $this->pink_ride === '0') {
            return false;
        }

        // Explicitly enabled by admin flag.
        if ((string) $this->pink_ride === '1') {
            return true;
        }

        // Fallback to the same business rules used to build the tooltip/error message.
        return $this->pinkRideEligibilityError($pinkRideSetting) === null;
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

        $folkRideSetting = $folkRideSetting ?: FolkRideSetting::getCached();

        if (!$folkRideSetting) {
            return true;
        }

        $overallRating = $overallRating ?? $this->driverOverallRating();
        $totalRides = $totalRides ?? $this->completedRideCount();
        $noShowsCount = $noShowsCount ?? $this->recentDriverNoShowsCount();
        $cancellationCount = $cancellationCount ?? $this->recentDriverCancellationCount();
        $noshows = $noshows ?? $this->recentAnyDriverNoShowsCount();
        $age = $this->age();

        if ($folkRideSetting->requiresVerifiedPhone() && !$this->hasVerifiedPhone()) {
            return false;
        }

        if ($folkRideSetting->requiresVerifiedEmail() && (string) $this->email_verified !== '1') {
            return false;
        }

        if ($folkRideSetting->requiresDriverLicense() && (string) $this->driver !== '1') {
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

    public function extraRideEligibilityError(
        ?FolkRideSetting $folkRideSetting = null,
        ?float $overallRating = null,
        ?int $totalRides = null,
        ?int $noShowsCount = null,
        ?int $cancellationCount = null,
        ?int $noshows = null
    ): ?string {
        if ((string) $this->folks_ride === '0') {
            return 'You are not allowed to post Extra Care Rides. Please contact support if you believe this is an error.';
        }

        if ((string) $this->folks_ride === '1') {
            return null;
        }

        $folkRideSetting = $folkRideSetting ?: FolkRideSetting::getCached();

        if (!$folkRideSetting) {
            return null;
        }

        $overallRating = $overallRating ?? $this->driverOverallRating();
        $totalRides = $totalRides ?? $this->completedRideCount();
        $noShowsCount = $noShowsCount ?? $this->recentDriverNoShowsCount();
        $cancellationCount = $cancellationCount ?? $this->recentDriverCancellationCount();
        $noshows = $noshows ?? $this->recentAnyDriverNoShowsCount();
        $age = $this->age();

        if ($folkRideSetting->requiresVerifiedPhone() && !$this->hasVerifiedPhone()) {
            return 'A verified phone number is required to post Extra Care Rides.';
        }

        if ($folkRideSetting->requiresVerifiedEmail() && (string) $this->email_verified !== '1') {
            return 'A verified email is required to post Extra Care Rides.';
        }

        if ($folkRideSetting->requiresDriverLicense()) {
            if ((string) $this->driver !== '1') {
                return 'Driver verification is required to post Extra Care Rides.';
            }

            if (!$this->hasDriverLicenseUpload()) {
                return 'A government-issued photo ID (driver\'s license) is required to post Extra Care Rides. Please upload your driver\'s license in your profile.';
            }
        }

        if (empty($this->government_issued_id ?? $this->government_id ?? null) || empty($this->address ?? '')) {
            return 'A complete address and government-issued ID are required to post Extra Care Rides.';
        }

        if ($noShowsCount > 0) {
            return 'Drivers with recent no-shows cannot post Extra Care Rides.';
        }

        if ($cancellationCount > 0 || $noshows > 0) {
            return 'Drivers with recent cancellations cannot post Extra Care Rides.';
        }

        $minRating = (float) ($folkRideSetting->average_rating ?? 0);
        if ($overallRating < $minRating) {
            return 'Extra Care Rides require a minimum driver rating of ' . $minRating . ' stars. Your current rating is ' . number_format((float) $overallRating, 1) . '.';
        }

        $minAge = (int) ($folkRideSetting->driver_age ?? 0);
        if ($minAge > 0 && $age < $minAge) {
            return 'Extra Care Rides require drivers to be at least ' . $minAge . ' years old.';
        }

        $rideLimit = (int) ($folkRideSetting->extra_rides_trip_limit ?? 0);
        if ($rideLimit > 0 && $totalRides < $rideLimit) {
            return 'Extra Care Rides require at least ' . $rideLimit . ' completed rides.';
        }

        if ($this->home_address == '') {
            return 'You have to address.';
        }

        return null;
    }

    public function pinkRideEligibilityError(?PinkRideSetting $pinkRideSetting = null): ?string
    {
        $pinkRideSetting = $pinkRideSetting ?: PinkRideSetting::getCached();

        if (!$pinkRideSetting) {
            return null;
        }

        if ($pinkRideSetting->requiresFemaleDriver()) {
            if ((string) $this->pink_ride !== '1') {
                if ($this->isPinkRideDisabled()) {
                    return 'You are not allowed to post Pink Rides. Please contact support if you believe this is an error.';
                }

                if (!$this->isFemale()) {
                    return 'Only female drivers can post Pink Rides.';
                }
            }
        }

        if ($pinkRideSetting->requiresDriverLicense() && !$this->hasDriverLicenseUpload()) {
            return 'A government-issued photo ID (driver\'s license) is required to post Pink Rides. Please upload your driver\'s license in your profile.';
        }

        if ($this->home_address == '') {
            return 'You have to address.';
        }



        return null;
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

        $pinkRideSetting = $pinkRideSetting ?: PinkRideSetting::getCached();

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

        if ($pinkRideSetting->requiresVerifiedPhone() && !$this->hasPinkRideVerifiedPhone()) {
            $parts[] = $postRidePage->pink_ride_tooltip_phone_number_text ?? '';
        }

        if ($pinkRideSetting->requiresVerifiedEmail() && (string) $this->email_verified !== '1') {
            $parts[] = $postRidePage->pink_ride_tooltip_email_text ?? '';
        }

        if ($pinkRideSetting->requiresDriverLicense() && (string) $this->driver !== '1') {
            $parts[] = $postRidePage->pink_ride_tooltip_driver_license_text ?? '';
        }

        $parts[] = $postRidePage->pink_ride_tooltip_verified_text ?? '';
        $parts[] = $postRidePage->pink_ride_tooltip_select_this_ride_text ?? '';

        return trim(implode(' ', array_filter($parts)));
    }

    public function extraRideTooltip(
        ?PostRidePageSettingDetail $postRidePage = null,
        ?FolkRideSetting $folkRideSetting = null
    ): string {
        $postRidePage = $postRidePage ?: $this->currentPostRidePage();

        if ((string) $this->folks_ride === '0') {
            return $postRidePage->extra_care_tooltip_admin_disable_text ?? '';
        }

        if ((string) $this->folks_ride === '1') {
            return $postRidePage->extra_care_tooltip_admin_enable_text ?? '';
        }

        $folkRideSetting = $folkRideSetting ?: FolkRideSetting::getCached();

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

        if ($folkRideSetting->requiresVerifiedPhone() && !$this->hasVerifiedPhone()) {
            $parts[] = $postRidePage->extra_care_tooltip_phone_number_text ?? '';
        }

        if ($folkRideSetting->requiresVerifiedEmail() && (string) $this->email_verified !== '1') {
            $parts[] = $postRidePage->extra_care_tooltip_email_text ?? '';
        }

        if ($folkRideSetting->requiresDriverLicense() && (string) $this->driver !== '1') {
            $parts[] = $postRidePage->extra_care_tooltip_driver_license_text ?? '';
        }

        if (
            $folkRideSetting->requiresVerifiedPhone() ||
            $folkRideSetting->requiresVerifiedEmail() ||
            $folkRideSetting->requiresDriverLicense()
        ) {
            $parts[] = $postRidePage->extra_care_tooltip_verified_text ?? '';
        }

        $parts[] = $postRidePage->extra_care_tooltip_eligible_text ?? '';

        return trim(implode(' ', array_filter($parts)));
    }

    public function primaryPhone()
    {
        return $this->hasOne(PhoneNumber::class, 'user_id')->where('default', 1);
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

    public function isBlockedPostRide(): bool
    {
        return (string) $this->block_post_ride === '1';
    }

    public function hasCustomProfileImage(): bool
    {
        if (!isset($this->profile_image) || $this->profile_image === '') {
            return false;
        }

        return !in_array(basename($this->profile_image), ['male.png', 'female.png', 'neutral.png']);
    }

    public function isSuspended(): bool
    {
        return (string) $this->suspand === '1';
    }

    public function isPinkRideDisabled(): bool
    {
        return (string) $this->pink_ride === '0';
    }

    public function isFemale(): bool
    {
        return strtolower((string) $this->gender) === 'female';
    }

    public function hasDriverLicenseUpload(): bool
    {
        return !empty($this->driver_license_upload);
    }

    public function driverPostRideStats(): array
    {
        $noShowsCount = $this->recentDriverNoShowsCount();

        return [
            'overallRating' => $this->displayDriverOverallRating(),
            'totalRides' => $this->completedRideCount(),
            'noShowsCount' => $noShowsCount,
            'cancellationCount' => $this->recentDriverCancellationCount(),
            // Kept for compatibility with existing Blade calls that still expect `noshows`.
            'noshows' => $noShowsCount,
        ];
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

    /**
     * Get the user's age in years.
     *
     * @return int The age in years, or 0 if date of birth is not set
     */
    public function getAge(): int
    {
        return $this->age();
    }

    public function getDisplayName(): string
    {
        return match ((string) ($this->type ?? '')) {
            '2' => trim((string) ($this->last_name ?? '')),
            '3' => trim(((string) ($this->first_name ?? '')) . ' ' . ((string) ($this->last_name ?? ''))),
            default => trim((string) ($this->first_name ?? '')),
        };
    }

    /**
     * Get the number of passengers whose bookings are completed on rides created by this user (as a driver).
     *
     * @return int The count of completed passenger bookings
     */
    public function getCompletedPassengerBookingsCount(): int
    {
        return Booking::whereHas('ride', function ($query) {
            $query->where('added_by', $this->id);
        })
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();
    }

    public function getPassengersDrivenCount(): int
    {
        $now = now();

        return $this->rides()
            ->notCancelled()
            ->where(function ($query) use ($now) {
                $query->whereDate('rides.date', '<', $now->toDateString())
                    ->orWhere(function ($query) use ($now) {
                        $query->whereDate('rides.date', '=', $now->toDateString())
                            ->whereTime('rides.time', '<=', $now->toTimeString());
                    });
            })
            ->get()
            ->flatMap(function ($ride) {
                return $ride->bookings()->pluck('seats');
            })
            ->sum();
    }

    public function getTakenRidesCount(): int
    {
        return $this->rides()
            ->notCancelled()
            ->where(function ($query) {
                $query->whereDate('rides.date', '<', now()->toDateString())
                    ->orWhere(function ($query) {
                        $query->whereDate('rides.date', '=', now()->toDateString())
                            ->whereTime('rides.time', '<=', now()->toTimeString());
                    });
            })
            ->count();
    }

    public function getTakenDistanceByDriver(): float
    {
        return $this->rides()
            ->notCancelled()
            ->where(function ($query) {
                $query->whereDate('rides.date', '<', now()->toDateString())
                    ->orWhere(function ($query) {
                        $query->whereDate('rides.date', '=', now()->toDateString())
                            ->whereTime('rides.time', '<=', now()->toTimeString());
                    });
            })
            ->with('rideDetail')
            ->get()
            ->sum(fn($r) => (float) ($r->rideDetail?->total_distance ?? 0));

            
    }

    public function getPassengerAverageRating(): float
    {
        return (float) Rating::where('status', 1)
            ->where('type', '2')
            ->whereHas('booking', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->avg('average_rating');
    }

    public function hasPassengerRatings(): bool
    {
        return Rating::where('status', 1)
            ->where('type', '2')
            ->whereHas('booking', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->exists();
    }

    protected function displayDriverOverallRating(): float
    {
        $overallRating = $this->driverOverallRating();

        return $overallRating > 0 ? $overallRating : 5.0;
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

    public const STUDENT_STATUS_NONE = 0;
    public const STUDENT_STATUS_VERIFIED = 1;
    public const STUDENT_STATUS_PENDING = 2;

    public const CHARGE_BOOKING_CHARGED = 1;
    public const CHARGE_BOOKING_WAIVED = 2;

    public function isStudent(): bool
    {
        return (int) ($this->student ?? 0) !== self::STUDENT_STATUS_NONE;
    }

    public function isVerifiedStudent(): bool
    {
        return (int) ($this->student ?? 0) === self::STUDENT_STATUS_VERIFIED;
    }

    public function isPendingStudent(): bool
    {
        return (int) ($this->student ?? 0) === self::STUDENT_STATUS_PENDING;
    }

    public function studentStatus(): int
    {
        return (int) ($this->student ?? 0);
    }

    public function hasBookingFeeWaiverFlag(): bool
    {
        return (int) ($this->charge_booking ?? 0) === self::CHARGE_BOOKING_WAIVED;
    }

    public function hasBookingChargeFlag(): bool
    {
        return (int) ($this->charge_booking ?? 0) === self::CHARGE_BOOKING_CHARGED;
    }

    public function isBookingFeeCurrentlyWaived(): bool
    {
        // Mirror BookingController::validateStudentBookingFee logic in a reusable way.
        if (!$this->hasBookingFeeWaiverFlag()) {
            return false;
        }

        // If verified student with an expiration date, only waive if card is not expired.
        if ($this->isVerifiedStudent() && !empty($this->student_card_exp_date)) {
            try {
                $expirationDate = \Carbon\Carbon::parse($this->student_card_exp_date);

                if ($expirationDate->isPast()) {
                    return false;
                }
            } catch (\Exception $e) {
                // On parse error, fall back to the raw flag.
            }
        }

        return true;
    }

    protected function completedRideCount(): int
    {
        return Ride::where('added_by', $this->id)
            ->notCancelled()
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

    protected function recentDriverCancellationCount($months = 3): int
    {
        return CancellationHistory::where('user_id', $this->id)
            ->where('type', 'driver')
            ->whereBetween('created_at', [now()->subMonths($months), now()])
            ->whereNotNull('booking_id')
            ->count();
    }

    public function recentPassengerCancellationCount($months = 3): int
    {
        return CancellationHistory::where('user_id', $this->id)
            ->where('type', 'passenger')
            ->whereBetween('created_at', [now()->subMonths($months), now()])
            ->whereNotNull('booking_id')
            ->count();
    }

    protected function recentAnyDriverNoShowsCount(): int
    {
        return $this->recentDriverNoShowsCount();
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

    public function getPassengerSeatsCount($ride_id): int
    {
        return (int) Booking::query()
            ->join('rides', 'rides.id', '=', 'bookings.ride_id')
            ->where('bookings.user_id', $this->id)
            ->where('bookings.ride_id', $ride_id)
            ->where('bookings.status', '<>', Booking::STATUS_CANCELLED)
            ->where('bookings.status', '<>', Booking::STATUS_DECLINED)
            ->where('rides.added_by', '!=', $this->id)
            ->sum('bookings.seats');
    }

    public function hasBookingRating($ride_id): bool
    {
        return Rating::query()
            ->join('bookings', 'bookings.id', '=', 'ratings.posted_to')
            ->where('bookings.user_id', $this->id)
            ->where('bookings.ride_id', $ride_id)
            ->exists();
    }

}
