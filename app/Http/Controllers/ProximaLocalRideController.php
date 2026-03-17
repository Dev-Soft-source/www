<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChatsPageSettingDetail;
use App\Models\FeaturesSettingDetail;
use Carbon\Carbon;
use App\Models\FindRidePageSettingDetail;
use App\Models\Language;
use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\PostRidePageSettingDetail;
use App\Models\Rating;
use App\Models\RecentSearch;
use App\Models\Ride;
use App\Models\SuccessMessagesSettingDetail;
use Illuminate\Http\Request;

class ProximaLocalRideController extends Controller
{
    public function SearchRide(Request $request, $lang = null)
    {
        $rides = null;
        $otherRides = null;
        $paginatedRides = null;

        $languages = Language::all();
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        
        $findRidePage = $this->getFindRidePageWithSettingDetail();

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        if ($request->from && $request->to) {
            if (auth()->user()) {
                if (auth()->user()->suspand === '1') {
                    return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
                }
            }

            $from = $request->from;
            $to = $request->to;

            // ProximaLocal rides: price per seat under $15
            $rides = Ride::whereHas('rideDetail', function ($q) use ($from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%')
                    ->where('price', '<', 15)
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('date', '>', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('date', '=', now()->toDateString())
                                        ->whereTime('time', '>=', now()->toTimeString());
                                });
                        });
                    });
            })->with(['rideDetail' => function ($q) use ($from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%');
            }])
                ->notCancelled()
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null);

            // Other rides: same route but price >= $15
            $otherRides = Ride::whereHas('rideDetail', function ($q) use ($from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%')
                    ->where('price', '>=', 15)
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereDate('date', '>', now()->toDateString())
                                ->orWhere(function ($query) {
                                    $query->whereDate('date', '=', now()->toDateString())
                                        ->whereTime('time', '>=', now()->toTimeString());
                                });
                        });
                    });
            })->with(['rideDetail' => function ($q) use ($from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%');
            }])
                ->notCancelled()
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null);

            if (auth()->user()) {
                $user_id = auth()->user()->id;
                $currentDate = date('Y-m-d H:i:s');
                $userBookings = Booking::where('user_id', $user_id)
                    ->where('removed_permanently', 1)
                    ->where('block_date_time', '>', $currentDate)
                    ->with('ride')
                    ->get();
                $addedByValues = $userBookings->pluck('ride.added_by')->unique()->toArray();
                $rides = $rides->whereNotIn('added_by', $addedByValues);
                $otherRides = $otherRides->whereNotIn('added_by', $addedByValues);
            }

            if ($request->date) {
                $dateForQuery = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
                $rides = $rides->where('date', $dateForQuery);
                $otherRides = $otherRides->where('date', $dateForQuery);
            }
            if ($request->driver_age) {
                $rides = $rides->whereHas('driver', fn($query) => $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(dob, "%M %d, %Y")) >= ?', [$request->driver_age]));
                $otherRides = $otherRides->whereHas('driver', fn($query) => $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(dob, "%M %d, %Y")) >= ?', [$request->driver_age]));
            }
            if ($request->driver_phone == 1) {
                $rides = $rides->whereHas('driver', fn($query) => $query->where('phone', '!=', ''));
                $otherRides = $otherRides->whereHas('driver', fn($query) => $query->where('phone', '!=', ''));
            }
            if ($request->driver_name) {
                $rides = $rides->whereHas('driver', fn($query) => $query->where('first_name', $request->driver_name));
                $otherRides = $otherRides->whereHas('driver', fn($query) => $query->where('first_name', $request->driver_name));
            }
            if ($request->passenger_rating) {
                $rides->where('features', 'like', '%' . $request->passenger_rating . '%');
                $otherRides->where('features', 'like', '%' . $request->passenger_rating . '%');
            }
            if ($request->payment_method) {
                $rides = $rides->where('payment_method', $request->payment_method);
                $otherRides = $otherRides->where('payment_method', $request->payment_method);
            }
            if ($request->vehicle_type) {
                $rides = $rides->where('vehicle_type', $request->vehicle_type);
                $otherRides = $otherRides->where('vehicle_type', $request->vehicle_type);
            }
            if ($request->luggage) {
                $luggages = explode(';', $request->luggage);
                $rides = $rides->whereIn('luggage', $luggages);
                $otherRides = $otherRides->whereIn('luggage', $luggages);
            }
            if ($request->smoking && $findRidePage && $findRidePage->smoking_option1) {
                $smoking = explode(';', $request->smoking);
                if (in_array($findRidePage->smoking_option1, $smoking)) {
                    $rides = $rides->whereIn('smoke', $smoking);
                    $otherRides = $otherRides->whereIn('smoke', $smoking);
                }
            }
            if ($request->pets) {
                $pets = explode(';', $request->pets);
                $rides = $rides->whereIn('animal_friendly', $pets);
                $otherRides = $otherRides->whereIn('animal_friendly', $pets);
            }
            if ($request->hide_full_rides) {
                $rides = $rides->whereRaw('rides.seats > (
                    SELECT COALESCE(SUM(bookings.seats), 0)
                    FROM bookings
                    INNER JOIN users ON bookings.user_id = users.id AND users.deleted_at IS NULL
                    WHERE bookings.ride_id = rides.id
                    AND bookings.status NOT IN (0, 1)
                )');
                $otherRides = $otherRides->whereRaw('rides.seats > (
                    SELECT COALESCE(SUM(bookings.seats), 0)
                    FROM bookings
                    INNER JOIN users ON bookings.user_id = users.id AND users.deleted_at IS NULL
                    WHERE bookings.ride_id = rides.id
                    AND bookings.status NOT IN (0, 1)
                )');
            }

            $rides = $rides->orderBy('date', 'asc')->orderBy('time', 'asc')->get()->map(fn($ride) => tap($ride, fn($r) => $r->type = 'ride'));
            $otherRides = $otherRides->orderBy('date', 'asc')->orderBy('time', 'asc')->get()->map(fn($ride) => tap($ride, fn($r) => $r->type = 'otherRide'));
            $allRides = $rides->merge($otherRides);

            // Only save to recent searches if the search returned at least one ProximaLocal ride (under $15)
            if (auth()->user() && $rides->count() > 0) {
                $existingSearch = RecentSearch::where('user_id', auth()->user()->id)
                    ->where('page_type', 'proximalocal_ride')
                    ->where('from', 'like', '%' . $request->from . '%')
                    ->where('to', 'like', '%' . $request->to . '%')
                    ->first();
                if ($existingSearch) {
                    $existingSearch->touch();
                } else {
                    RecentSearch::create([
                        'from' => $request->from,
                        'to' => $request->to,
                        'user_id' => auth()->user()->id,
                        'page_type' => 'proximalocal_ride',
                    ]);
                }
            }

            $paginatedRides = new LengthAwarePaginator(
                $allRides->forPage(Paginator::resolveCurrentPage(), 6),
                $allRides->count(),
                6,
                Paginator::resolveCurrentPage(),
                ['path' => Paginator::resolveCurrentPath()]
            );

            if ($request->driver_rating) {
                $filterByRating = function ($rideList, $minRating) {
                    return $rideList->filter(function ($ride) use ($minRating) {
                        $avg = Rating::where('type', '1')->whereHas('ride', fn($q) => $q->where('added_by', $ride->added_by))->where('status', 1)->avg('average_rating');
                        return ($avg ?? 0) >= $minRating;
                    });
                };
                $rides = $filterByRating($rides, $request->driver_rating);
                $otherRides = $filterByRating($otherRides, $request->driver_rating);
                $allRides = $rides->merge($otherRides);
                $paginatedRides = new LengthAwarePaginator($allRides->forPage(Paginator::resolveCurrentPage(), 6), $allRides->count(), 6, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
            }
        }

        $ratings = Rating::all();
        $recentSearches = RecentSearch::where('page_type', 'proximalocal_ride')->orderBy('updated_at', 'desc')->limit(3)->get();
        

        $extraCareFaqs = collect();

        return view('proximalocal_ride', [
            'postRidePage' => $postRidePage,
            'findRidePage' => $findRidePage,
            'extraCareFaqs' => $extraCareFaqs,
            'paginatedRides' => $paginatedRides ?? null,
            'recentSearches' => $recentSearches ?? collect(),
            'request' => $request,
            'ratings' => $ratings,
        ]);
    }
}
