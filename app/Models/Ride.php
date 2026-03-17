<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Concerns\HasOptionGroups;
use App\Models\Rating;
use App\Models\FeaturesSettingDetail;
use App\Models\Language;
use App\Models\Vehicle;

class Ride extends Model
{
    use HasFactory, HasOptionGroups;

    public const PINK_RIDE_FEATURE_ID = '1';
    public const EXTRA_CARE_RIDE_FEATURE_ID = '2';
    public const STATUS_ACTIVE = 1;
    public const STATUS_CANCELLED = 2;

    protected $fillable = ['random_id','departure','departure_lat','departure_lng','departure_place','departure_route','departure_zipcode','departure_city','departure_state','departure_state_short','departure_country',
        'destination','destination_lat','destination_lng','destination_place','destination_route','destination_zipcode','destination_city','destination_state','destination_state_short','destination_country',
        'total_distance','total_time','date','time','completed_date','completed_time','recurring','recurring_type','recurring_trips','recurring_id','details','seats','vehicle_mode','skip_vehicle','add_vehicle','added_vehicle','vehicle_id','make','model','vehicle_type','year','color','license_no','car_type','car_image','car_image_original','smoke','animal_friendly','features',
        'booking_method','booking_type','max_back_seats','luggage','accept_more_luggage','open_customized','price','payment_method','notes','added_by','until_date','until_limit','pickup','dropoff','middle_seats','back_seats',
        'status', 'added_on', 'remove_car_image'];

    public $timestamps = false;
    protected $casts = [
        'vehicle_type' => 'integer',
    ];
    
    function driver(){
        return $this->belongsTo(User::class, 'added_by')->withTrashed();
    }

    function vehicle(){
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    function bookings(){
        return $this->hasMany(Booking::class, 'ride_id');
    }
    
    function detail(){
        return $this->hasOne(RideDetail::class, 'ride_id');
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'active',
            self::STATUS_CANCELLED => 'cancelled',
        ];
    }

    function rideDetail(){
        return $this->hasMany(RideDetail::class, 'ride_id', 'id');
    }

    public function rideStops()
    {
        return $this->hasMany(RideStop::class, 'ride_id')->orderBy('stop_order');
    }

    public function rideStopSegments()
    {
        return $this->hasMany(RideStopSegment::class, 'ride_id');
    }

    function defaultRideDetail(){
        return $this->hasMany(RideDetail::class, 'ride_id', 'id')->where('default_ride', '1');
    }

    function MoreRideDetail(){
        return $this->hasMany(RideDetail::class, 'ride_id', 'id')->where('default_ride', '0');
    }

    public function scopeActiveStatus($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeNotCancelled($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    function ratings(){
        return $this->hasMany(Rating::class, 'ride_id');
    }

    /**
     * Get the average rating value from all ratings for this ride
     * 
     * @param int|null $status Filter by status (1 or 2)
     * @param int|null $type Filter by type (1 or 2)
     * @return float|null
     */
    public function getAverageRating($status = null, $type = null, $booking_user_id = null)
    {
        $query = $this->ratings();
        
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        if ($type !== null) {
            $query->where('type', $type);
        }

        if($booking_user_id !== null){
            $query->whereHas('booking', function($q) use ($booking_user_id) {
                $q->where('user_id', $booking_user_id);
            });
        }
        
        return $query->avg('average_rating');
    }

    public function getHasRatings($status = null, $type = null, $booking_user_id = null)
    {
        $query = $this->ratings();
        
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        if ($type !== null) {
            $query->where('type', $type);
        }

        if($booking_user_id !== null){
            $query->whereHas('booking', function($q) use ($booking_user_id) {
                $q->where('user_id', $booking_user_id);
            });
        }
        
        return $query->exists();
    }

    /**
     * Get the driver's overall average rating (across all rides by this driver).
     * Type 1 = driver ratings, Status 1 = approved.
     *
     * @return float|null
     */
    public function getDriverAverageRating()
    {
        return Rating::where('type', 1)
            ->where('status', 1)
            ->whereHas('ride', function ($q) {
                $q->where('added_by', $this->added_by);
            })
            ->avg('average_rating');
    }

    /**
     * Check if the driver has any ratings (across all their rides).
     *
     * @return bool
     */
    public function getDriverHasRatings()
    {
        return Rating::where('type', 1)
            ->where('status', 1)
            ->whereHas('ride', function ($q) {
                $q->where('added_by', $this->added_by);
            })
            ->exists();
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

    /**
     * All seat details for the ride, ordered by seat_number (left to right).
     */
    public function seatDetail()
    {
        return $this->hasMany(SeatDetail::class, 'ride_id')->orderBy('seat_number');
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

    public static function normalizeRideVehicleTypeId($value): ?int
    {
        return Vehicle::normalizeVehicleTypeId($value);
    }

    public function getVehicleTypeLabelAttribute(): ?string
    {
        $featureId = self::normalizeRideVehicleTypeId($this->attributes['vehicle_type'] ?? null);

        if (!$featureId) {
            return null;
        }

        $selectedLanguage = Language::resolveLanguage(session('selectedLanguage'));
        $defaultLanguageId = Language::where('is_default', 1)->value('id') ?? 1;

        $detail = FeaturesSettingDetail::where('features_setting_id', $featureId)
            ->whereIn('language_id', array_filter([$selectedLanguage?->id, $defaultLanguageId]))
            ->get()
            ->sortByDesc(fn ($item) => (int) ($selectedLanguage && $item->language_id == $selectedLanguage->id))
            ->first();

        return $detail?->name;
    }

    public function isPinkRide(): bool
    {
        return in_array(self::PINK_RIDE_FEATURE_ID, $this->normalizeFeatureIds($this->features), true);
    }

    public function isExtraCareRide(): bool
    {
        return in_array(self::EXTRA_CARE_RIDE_FEATURE_ID, $this->normalizeFeatureIds($this->features), true);
    }
    
    public function isPinkExtraCareRide(): bool
    {
        return $this->isExtraCareRide() && $this->isPinkRide();
    }

    public function pricePerSeat(): float
    {
        return (float) ($this->rideDetail->first()?->price/100 ?? 0);
    }

    public function isShortDistanceRide(): bool
    {
        $pricePerSeat = $this->pricePerSeat();

        return $pricePerSeat > 0 && $pricePerSeat <= 15;
    }

    public static function searchRides(array $filters, ?User $user = null): LengthAwarePaginator
    {
        $query = static::query()
            ->with([
                'driver',
                'vehicle',
                'bookings',
                'rideStops' => fn ($q) => $q->orderBy('stop_order'),
                'rideStopSegments',
                'rideDetail',
                'pendingSeatDetail',
            ])
            ->notCancelled()
            ->where(function ($q) {
                $q->whereDate('date', '>', now()->toDateString())
                    ->orWhere(function ($sameDay) {
                        $sameDay->whereDate('date', now()->toDateString())
                            ->whereTime('time', '>=', now()->toTimeString());
                    });
            });

        if ($user) {
            $query->where('added_by', '!=', (int) $user->id);
        }

        if (!empty($filters['exclude_admin_deactive'])) {
            $query->where('suspand', '!=', 1);
        }

        if (!empty($filters['require_vehicle'])) {
            $query->whereNotNull('vehicle_id');
        }

        if (!empty($filters['excluded_driver_ids']) && is_array($filters['excluded_driver_ids'])) {
            $query->whereNotIn('added_by', array_map('intval', $filters['excluded_driver_ids']));
        }

        static::applyOrderedStopFilters($query, $filters);
        static::applyKeywordFilters($query, trim((string) ($filters['keyword'] ?? '')));
        static::applyRideFilters($query, $filters);

        if (!empty($filters['departure_date'])) {
            $date = (string) $filters['departure_date'];
            $query->where(function (Builder $dateQuery) use ($date) {
                $dateQuery->whereDate('date', $date)
                    ->orWhereHas('rideStops', function (Builder $stopQuery) use ($date) {
                        $stopQuery->whereDate('departure_at', $date);
                    });
            });
        }

        $sort = (string) ($filters['sort'] ?? 'soonest');
        if ($sort === 'latest_added') {
            $query->orderByDesc('id');
        } else {
            $query->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->orderByDesc('id');
        }

        $perPage = max(1, (int) ($filters['per_page'] ?? 20));
        $rides = $query->paginate($perPage);

        $rides->getCollection()->transform(function (self $ride) {
            $orderedStops = $ride->rideStops->sortBy('stop_order')->values();
            $firstStop = $orderedStops->first();
            $lastStop = $orderedStops->last();
            $departureAt = $firstStop?->departure_at ?: trim(($ride->date ?? '') . ' ' . ($ride->time ?? ''));
            $priceMinor = (int) round((float) ($ride->pricePerSeat()));

            $ride->setRelation('stops', $orderedStops);
            $ride->setRelation('route', (object) [
                'origin_label' => $firstStop?->label ?: $ride->departure,
                'destination_label' => $lastStop?->label ?: $ride->destination,
            ]);
            $ride->setRelation('options', collect());

            $ride->meta = [
                'pickup_location' => $ride->pickup,
                'dropoff_location' => $ride->dropoff,
            ];
            $ride->departure_at = $departureAt ?: null;
            $ride->price_minor = $priceMinor;
            $ride->price_per_seat_minor = $priceMinor;
            // $ride->currency = 'USD';
            $ride->seats_total = (int) ($ride->seats ?? 0);
            $ride->seats_available = $ride->pendingSeatDetail->count() ?: max(0, (int) ($ride->seats ?? 0));
            $ride->detail_route = 'ride_detail';
            $ride->detail_query = [
                'departure' => $firstStop?->label ?: $ride->departure,
                'destination' => $lastStop?->label ?: $ride->destination,
            ];

            return $ride;
        });

        return $rides;
    }

    protected static function applyOrderedStopFilters(Builder $query, array $filters): void
    {
        $fromCityId = $filters['origin_city_id'] ?? null;
        $toCityId = $filters['destination_city_id'] ?? null;
        $fromLabel = trim((string) ($filters['origin_label'] ?? ''));
        $toLabel = trim((string) ($filters['destination_label'] ?? ''));

        $hasFrom = !empty($fromCityId) || $fromLabel !== '';
        $hasTo = !empty($toCityId) || $toLabel !== '';

        if ($hasFrom && $hasTo) {
            $query->where(function (Builder $rideQuery) use ($fromCityId, $toCityId, $fromLabel, $toLabel) {
                $rideQuery->whereExists(function ($sub) use ($fromCityId, $toCityId, $fromLabel, $toLabel) {
                    $sub->select(DB::raw(1))
                        ->from('ride_stops as s_from')
                        ->join('ride_stops as s_to', function ($join) {
                            $join->on('s_to.ride_id', '=', 's_from.ride_id')
                                ->whereColumn('s_from.stop_order', '<', 's_to.stop_order');
                        })
                        ->whereColumn('s_from.ride_id', 'rides.id');

                    static::applyStopMatchToQuery($sub, 's_from', $fromCityId, $fromLabel);
                    static::applyStopMatchToQuery($sub, 's_to', $toCityId, $toLabel);
                })->orWhereHas('rideDetail', function (Builder $detailQuery) use ($fromLabel, $toLabel) {
                    $detailQuery->where('departure', 'like', '%' . $fromLabel . '%')
                        ->where('destination', 'like', '%' . $toLabel . '%');
                });
            });

            return;
        }

        if ($hasFrom) {
            $query->where(function (Builder $rideQuery) use ($fromCityId, $fromLabel) {
                $rideQuery->whereHas('rideStops', function (Builder $stopQuery) use ($fromCityId, $fromLabel) {
                    static::applyStopMatchToQuery($stopQuery, 'ride_stops', $fromCityId, $fromLabel);
                })->orWhere('departure_city', 'like', '%' . $fromLabel . '%')
                    ->orWhere('departure', 'like', '%' . $fromLabel . '%');
            });
        }

        if ($hasTo) {
            $query->where(function (Builder $rideQuery) use ($toCityId, $toLabel) {
                $rideQuery->whereHas('rideStops', function (Builder $stopQuery) use ($toCityId, $toLabel) {
                    static::applyStopMatchToQuery($stopQuery, 'ride_stops', $toCityId, $toLabel);
                })->orWhere('destination_city', 'like', '%' . $toLabel . '%')
                    ->orWhere('destination', 'like', '%' . $toLabel . '%');
            });
        }
    }

    protected static function applyKeywordFilters(Builder $query, string $keyword): void
    {
        if ($keyword === '') {
            return;
        }

        $query->where(function (Builder $keywordQuery) use ($keyword) {
            $keywordQuery->where('pickup', 'like', '%' . $keyword . '%')
                ->orWhere('dropoff', 'like', '%' . $keyword . '%')
                ->orWhere('details', 'like', '%' . $keyword . '%')
                ->orWhere('notes', 'like', '%' . $keyword . '%')
                ->orWhere('departure', 'like', '%' . $keyword . '%')
                ->orWhere('destination', 'like', '%' . $keyword . '%')
                ->orWhereHas('rideStops', function (Builder $stopQuery) use ($keyword) {
                    $stopQuery->where('label', 'like', '%' . $keyword . '%')
                        ->orWhere('pickup_dropoff_location', 'like', '%' . $keyword . '%');
                })
                ->orWhereHas('rideDetail', function (Builder $detailQuery) use ($keyword) {
                    $detailQuery->where('departure', 'like', '%' . $keyword . '%')
                        ->orWhere('destination', 'like', '%' . $keyword . '%')
                        ->orWhere('pickup', 'like', '%' . $keyword . '%')
                        ->orWhere('dropoff', 'like', '%' . $keyword . '%');
                });
        });
    }

    protected static function applyRideFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['driver_age'])) {
            $query->whereHas('driver', function (Builder $driverQuery) use ($filters) {
                $driverQuery->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(dob, "%M %d, %Y")) >= ?', [(int) $filters['driver_age']]);
            });
        }

        if (!empty($filters['driver_phone'])) {
            $query->whereHas('driver', function (Builder $driverQuery) {
                $driverQuery->where('phone', '!=', '');
            });
        }

        if (!empty($filters['driver_name'])) {
            $name = trim((string) $filters['driver_name']);
            $query->whereHas('driver', function (Builder $driverQuery) use ($name) {
                $driverQuery->where(function (Builder $nameQuery) use ($name) {
                    $nameQuery->where('first_name', 'like', '%' . $name . '%')
                        ->orWhere('last_name', 'like', '%' . $name . '%')
                        ->orWhere('name', 'like', '%' . $name . '%');
                });
            });
        }

        if (!empty($filters['booking_method'])) {
            $query->where('booking_method', $filters['booking_method']);
        }

        if (!empty($filters['vehicle_type'])) {
            $query->where('vehicle_type', self::normalizeRideVehicleTypeId($filters['vehicle_type']));
        }

        if (!empty($filters['luggage_size'])) {
            $query->where('luggage', $filters['luggage_size']);
        }

        if (!empty($filters['smoking_allowed'])) {
            $query->where('smoke', $filters['smoking_allowed']);
        }

        if (!empty($filters['pets_allowed'])) {
            $query->where('animal_friendly', $filters['pets_allowed']);
        }

        if (!empty($filters['women_only'])) {
            $query->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [self::PINK_RIDE_FEATURE_ID]);
        }

        if (!empty($filters['extra_care'])) {
            $query->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [self::EXTRA_CARE_RIDE_FEATURE_ID]);
        }

        if (!empty($filters['ride_option_ids']) && is_array($filters['ride_option_ids'])) {
            foreach ($filters['ride_option_ids'] as $featureId) {
                $query->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [(int) $featureId]);
            }
        }

        if (!empty($filters['hide_full_rides'])) {
            $query->whereHas('pendingSeatDetail');
        }

        if (!empty($filters['driver_rating'])) {
            $query->whereRaw(
                'COALESCE((SELECT AVG(ratings.average_rating) FROM ratings INNER JOIN rides AS driver_rides ON driver_rides.id = ratings.ride_id WHERE ratings.status = 1 AND ratings.type = 1 AND driver_rides.added_by = rides.added_by), 0) >= ?',
                [(float) $filters['driver_rating']]
            );
        }
    }

    protected static function applyStopMatchToQuery($query, string $table, $cityId, string $label): void
    {
        $query->where(function ($stopQuery) use ($table, $cityId, $label) {
            if (!empty($cityId)) {
                $stopQuery->where($table . '.city_id', $cityId);
            }

            if ($label !== '') {
                $method = !empty($cityId) ? 'orWhere' : 'where';
                $stopQuery->{$method}($table . '.label', 'like', '%' . $label . '%');
            }
        });
    }

    protected function normalizeFeatureIds($features = null): array
    {
        $features = $features ?? $this->features;

        if (is_string($features)) {
            $features = explode('=', $features);
        }

        if (!is_array($features)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($feature) {
            if ($feature === null) {
                return null;
            }

            $feature = trim((string) $feature);

            return $feature === '' ? null : $feature;
        }, $features), fn ($feature) => $feature !== null));
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
