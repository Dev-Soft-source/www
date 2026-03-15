<?php

namespace App\Http\Controllers;

use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use App\Mail\PinkRideMail;
use App\Mail\RidePostedMail;
use App\Models\Booking;
use App\Models\BookingPageSettingDetail;
use App\Models\CancellationHistory;
use App\Models\CancelRideSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\FindRidePageSettingDetail;
use App\Models\FolkRideSetting;
use App\Models\Language;
use App\Models\Notification;
use App\Models\PinkRideSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\PostRidePageSettingSubDetail;
use App\Models\ChatsPageSettingDetail;
use App\Models\Rating;
use App\Models\RecentSearch;
use App\Models\ReviewSetting;
use App\Models\Ride;
use App\Models\RideDetail;
use App\Models\City;
use App\Models\FCMToken;
use App\Models\MyVehicleSettingDetail;
use App\Models\NoShowHistory;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SiteSetting;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\SiteTextDetail;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\SeatDetail;
use App\Services\FCMService;
use App\Models\PhoneNumber;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    public function SearchRide(Request $request, $lang = null)
    {

        $rides = null;

        $findRidePage = $this->getFindRidePageWithSettingDetail();
        $postRidePage = $this->getPostRidePageWithSettingDetail();
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        if (!auth()->user()) {
            $rides = Ride::where('status', '!=', 2)
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->whereDate('date', '>', now()->toDateString())
                            ->orWhere(function ($query) {
                                $query->whereDate('date', '=', now()->toDateString())
                                    ->whereTime('time', '>=', now()->toTimeString());
                            });
                    });
                })->with(['rideDetail' => function ($q) {
                    $q->where('default_ride', '1');
                }])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->paginate(6);
        }

        if ($request->from && $request->to) {
            if (auth()->user()) {
                // Check if user has suspanded
                if (auth()->user()->suspand === '1') {
                    return redirect()->route('home', ['lang' => $selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
                }

                // Check if the search already exists
                $existingSearch = RecentSearch::where('user_id', auth()->user()->id)
                    ->where('from', 'like', '%' . $request->from . '%')
                    ->where('to', 'like', '%' . $request->to . '%')
                    ->first();

                if ($existingSearch) {
                    // Update the updated_at timestamp
                    $existingSearch->touch();
                } else {
                    // Store recent search
                    RecentSearch::create([
                        'from' => $request->from,
                        'to' => $request->to,
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }

            $from = $request->from;
            $to = $request->to;
            $rides = Ride::whereHas('rideDetail', function ($q) use ($from, $to) {
                $q->where('departure', 'like', '%' . $from . '%')
                    ->where('destination', 'like', '%' . $to . '%')->where(function ($query) {
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
            }])->where('status', '!=', 2)
                ->where('suspand', '!=', 1)
                ->where('vehicle_id', '!=', null);


            // ->where(function ($query) {
            //     $query->where(function ($query) {
            //         $query->whereDate('date', '>', now()->toDateString())
            //             ->orWhere(function ($query) {
            //                 $query->whereDate('date', '=', now()->toDateString())
            //                     ->whereTime('time', '>=', now()->toTimeString());
            //             });
            //     });
            // });

            if (auth()->user()) {
                $user_id = auth()->user()->id;
                $currentDate = date('Y-m-d H:i:s');
                $userBookings = Booking::where('user_id', $user_id)
                    ->where('removed_permanently', 1)
                    ->where('block_date_time', '>', $currentDate)
                    ->with('ride')
                    ->get();

                // Get the added_by values from userBookings
                $addedByValues = $userBookings->pluck('ride.added_by')->unique()->toArray();

                // Add additional condition to the rides query
                $rides = $rides->whereNotIn('added_by', $addedByValues);
            }

            if ($request->keyword) {

                $keyword = $request->keyword;

                $rides = $rides->where(function ($query) use ($keyword) {
                    $query->where('dropoff', 'like', "%$keyword%")
                        ->orWhere('pickup', 'like', "%$keyword%")
                        ->orWhere('details', 'like', "%$keyword%")
                        ->orWhere('notes', 'like', "%$keyword%");
                });
            }

            if ($request->date) {
                // $dateForQuery = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
                $dateForQuery = Carbon::parse(strtotime($request->date))->format('Y-m-d');
                $rides = $rides->where('date', $dateForQuery);
            }
            if ($request->driver_age) {
                $rides = $rides->whereHas('driver', function ($query) use ($request) {
                    $query->whereRaw('YEAR(CURDATE()) - YEAR(STR_TO_DATE(dob, "%M %d, %Y")) >= ?', [$request->driver_age]);
                });
            }
            if ($request->driver_phone == 1) {
                $rides = $rides->whereHas('driver', function ($query) {
                    $query->where('phone', '!=', '');
                });
            }
            if ($request->driver_name) {
                $rides = $rides->whereHas('driver', function ($query) use ($request) {
                    $query->where('first_name', $request->driver_name);
                });
            }
            if ($request->passenger_rating) {
                $rides->where('features', 'like', '%' . $request->passenger_rating . '%');
            }
            if ($request->payment_method) {
                $rides = $rides->where('payment_method', $request->payment_method);
            }
            if ($request->vehicle_type) {
                $rides = $rides->where('vehicle_type', Ride::normalizeRideVehicleTypeId($request->vehicle_type));
            }
            if ($request->features) {
                $features = explode(';', $request->features);

                if (in_array($postRidePage->features_option4, $features)) {
                    $rides = $rides->where(function ($query) use ($postRidePage, $features) {
                        $query->where(function ($query) use ($postRidePage) {
                            $query->where('features', 'like', '%' . $postRidePage->features_option4 . '%')
                                ->orWhere('features', 'like', '%' . $postRidePage->features_option5 . '%');
                        });

                        // Check if any feature other than post ride features is present in the features array
                        if (count(array_diff($features, [$postRidePage->features_option4, $postRidePage->features_option5])) > 0) {
                            $query->where(function ($query) use ($features, $postRidePage) {
                                foreach ($features as $feature) {
                                    if ($feature != $postRidePage->features_option4 && $feature != $postRidePage->features_option5) {
                                        $query->where('features', 'like', '%' . $feature . '%');
                                    }
                                }
                            });
                        }
                    });
                }

                if (in_array($postRidePage->features_option6, $features)) {
                    $rides = $rides->where(function ($query) use ($postRidePage, $features) {
                        $query->where(function ($query) use ($postRidePage) {
                            $query->where('features', 'like', '%' . $postRidePage->features_option6 . '%')
                                ->orWhere('features', 'like', '%' . $postRidePage->features_option7 . '%');
                        });

                        // Check if any feature other than post ride features is present in the features array
                        if (count(array_diff($features, [$postRidePage->features_option6, $postRidePage->features_option7])) > 0) {
                            $query->where(function ($query) use ($features, $postRidePage) {
                                foreach ($features as $feature) {
                                    if ($feature != $postRidePage->features_option6 && $feature != $postRidePage->features_option7) {
                                        $query->where('features', 'like', '%' . $feature . '%');
                                    }
                                }
                            });
                        }
                    });
                }

                if (!in_array($postRidePage->features_option4, $features) && !in_array($postRidePage->features_option6, $features)) {
                    foreach ($features as $feature) {
                        $rides->whereRaw("FIND_IN_SET(?, REPLACE(features, '=', ','))", [$feature]);
                    }
                }
            }
            if ($request->luggage) {
                $luggages = explode(';', $request->luggage);
                $rides = $rides->whereIn('luggage', $luggages);
            }
            if ($request->smoking) {
                $smoking = explode(';', $request->smoking);
                if (in_array($findRidePage->smoking_option1, $smoking)) {
                    $rides = $rides->whereIn('smoke', $smoking);
                }
            }
            if ($request->pets) {
                $pets = explode(';', $request->pets);
                $rides = $rides->whereIn('animal_friendly', $pets);
            }
            if ($request->hide_full_rides) {
                $rides = $rides->whereRaw('rides.seats > (
                    SELECT COALESCE(SUM(bookings.seats), 0)
                    FROM bookings
                    INNER JOIN users ON bookings.user_id = users.id AND users.deleted_at IS NULL
                    WHERE bookings.ride_id = rides.id
                    AND bookings.status NOT IN (0, 1)
                )');
            }

            $rides = $rides->orderBy('date', 'asc')->orderBy('time', 'asc')->paginate(6);
            if ($request->driver_rating) {
                $filteredRides = [];
                foreach ($rides as $ride) {
                    $ratings = Rating::where(function ($query) use ($ride) {
                        // Ratings where type is 1 and ride_id belongs to the user
                        $query->where('type', '1')
                            ->whereHas('ride', function ($query) use ($ride) {
                                $query->where('added_by', $ride->added_by);
                            });
                    })
                        ->where('status', 1)
                        ->orderBy('id', 'desc')
                        ->get();

                    // Calculate total average
                    $overallRating = $ratings->avg('average_rating') ?? 0;
                    if ($overallRating >= $request->driver_rating) {
                        $filteredRides[] = $ride;
                    }
                }
                $rides = collect($filteredRides);

                // Now paginate the filtered rides
                $rides = new LengthAwarePaginator(
                    $rides->forPage(Paginator::resolveCurrentPage(), 6),
                    $rides->count(),
                    6,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );
            }
        }


        $recentSearches = RecentSearch::orderBy('updated_at', 'desc')->limit(2)->get();


        $pinkRideSetting = PinkRideSetting::first();
        $firm_cancellation_discount = SiteSetting::first();
        $firm_cancellation_discount = $firm_cancellation_discount->frim_discount;

        return view('search_ride', [
            'rideDetailPage' => $rideDetailPage,
            'pinkRideSetting' => $pinkRideSetting,
            'postRidePage' => $postRidePage,
            'findRidePage' => $findRidePage,
            'rides' => $rides,
            'recentSearches' => $recentSearches,
            'request' => $request,
            'firm_cancellation_discount' => $firm_cancellation_discount
        ]);
    }

    public function RideDetail(Request $request, $lang = null)
    {
        $id = $request->id;
        $from = $request->departure;
        $to = $request->destination;

        $ride = Ride::where('id', $request->id)
            ->with([
                'rideDetail' => function ($q) use ($from, $to, $id) {
                    $q->where('departure', 'like', '%' . $from . '%')
                        ->where('destination', 'like', '%' . $to . '%')
                        ->where('ride_id', $id);
                },
                'vehicle',
            ])
            ->first();

        if (!isset($ride) && empty($ride)) {
            $lang = $lang ?? 'en';
            return redirect(route('home', ['lang' => $lang]));
        }

        $setting = ReviewSetting::first();
        $cancelSetting = CancelRideSetting::first();
        $ratings = Rating::all();

        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );

        $chatsPage = ChatsPageSettingDetail::getByLanguageWithFallback(
            $this->selectedLanguage->id,
            $this->defaultLang->id
        );

        $postRidePage = $this->getPostRidePageWithSettingDetail();

        if ($ride) {
            // Optimized: Batch load all option groups in a single query instead of 5 separate queries
            $ride->mapMultipleOptionColumnsToDetails(
                ['luggage', 'payment_method', 'booking_type', 'animal_friendly', 'booking_method'],
                $this->selectedLanguage->id,
                $this->defaultLang->id,
                false
            );
        }

        $featureIds = array_filter(explode('=', $ride->features ?? ''));
        $ride->pink_ride = $postRidePage->features_option1 &&
            in_array((string) $postRidePage->features_option1->features_setting_id, $featureIds)
                ? $postRidePage->features_option1
                : null;
        $ride->extra_care_ride = $postRidePage->features_option2 &&
            in_array((string) $postRidePage->features_option2->features_setting_id, $featureIds)
                ? $postRidePage->features_option2
                : null;

        // Compute origin / stops / destination / pickup / dropoff for the full route
        $allDetails = $ride->rideDetail()->orderBy('id')->get();

        $defaultDetail = $allDetails->where('default_ride', 1)->first();
        $moreDetails = $allDetails->where('default_ride', 0)->sortBy('id');

        $origin = $defaultDetail && $defaultDetail->departure
            ? $defaultDetail->departure
            : ($allDetails->first() ? $allDetails->first()->departure : '');

        $destination = $defaultDetail && $defaultDetail->destination
            ? $defaultDetail->destination
            : ($allDetails->last() ? $allDetails->last()->destination : '');

        $segmentPickup = $defaultDetail && $defaultDetail->pickup
            ? $defaultDetail->pickup
            : ($allDetails->first() && $allDetails->first()->pickup
                ? $allDetails->first()->pickup
                : $ride->pickup);

        $segmentDropoff = $defaultDetail && $defaultDetail->dropoff
            ? $defaultDetail->dropoff
            : ($allDetails->last() && $allDetails->last()->dropoff
                ? $allDetails->last()->dropoff
                : $ride->dropoff);

        $orderedPoints = collect([$origin]);
        $current = $origin;
        $remaining = $moreDetails->values();

        while ($current !== $destination && $remaining->isNotEmpty()) {
            $nextSegment = $remaining->first(function ($d) use ($current) {
                return (string) $d->departure === (string) $current;
            });

            if (!$nextSegment) {
                break;
            }

            $orderedPoints->push($nextSegment->destination);
            $current = $nextSegment->destination;
            $remaining = $remaining->filter(function ($d) use ($nextSegment) {
                return $d->id != $nextSegment->id;
            });
        }

        // Full route endpoints: always the driver's origin (A) and destination (E), regardless of passenger search (e.g. B to D)
        if ($orderedPoints->isNotEmpty()) {
            $origin = $orderedPoints->first();
            $destination = $orderedPoints->last();
            $originSegment = $allDetails->first(function ($d) use ($origin) {
                return (string) $d->departure === (string) $origin;
            });
            $destSegment = $allDetails->first(function ($d) use ($destination) {
                return (string) $d->destination === (string) $destination;
            });
            if ($originSegment && !empty($originSegment->pickup)) {
                $segmentPickup = $originSegment->pickup;
            }
            if ($destSegment && !empty($destSegment->dropoff)) {
                $segmentDropoff = $destSegment->dropoff;
            }
        }

        // Stops: only the intermediate points between the passenger's search from/to (e.g. search A→D → stops B,C).
        // If no search segment, use all intermediates between full route origin and destination.
        $stopNames = collect();
        $fromIndex = $orderedPoints->search(function ($p) use ($from) {
            return (string) $p === (string) $from;
        });
        $toIndex = $orderedPoints->search(function ($p) use ($to) {
            return (string) $p === (string) $to;
        });
        if ($from !== null && $from !== '' && $to !== null && $to !== ''
            && $fromIndex !== false && $toIndex !== false && $fromIndex < $toIndex && ($toIndex - $fromIndex) >= 2) {
            // Points strictly between search from and search to
            $stopNames = $orderedPoints->slice($fromIndex + 1, $toIndex - $fromIndex - 1)->values();
        } else {
            // No search segment: show all intermediates between full route origin and destination
            $stopNames = $orderedPoints->count() > 2
                ? $orderedPoints->slice(1, $orderedPoints->count() - 2)->values()
                : collect();
        }

        // For each stop, find its specific pickup, dropoff, and date/time from the segment list:
        // - pickup: segment where this stop is the departure (with pickup note)
        // - dropoff: segment where this stop is the destination (with dropoff note)
        // - date/time: segment where this stop is the departure (departure date/time at this stop)
        $stops = $stopNames->map(function ($name) use ($allDetails) {
            $pickupSegment = $allDetails->first(function ($d) use ($name) {
                return (string) $d->departure === (string) $name && !empty($d->pickup);
            });
            $dropoffSegment = $allDetails->first(function ($d) use ($name) {
                return (string) $d->destination === (string) $name && !empty($d->dropoff);
            });
            $departureSegment = $allDetails->first(function ($d) use ($name) {
                return (string) $d->departure === (string) $name;
            });

            return [
                'name'    => $name,
                'pickup'  => $pickupSegment ? $pickupSegment->pickup : null,
                'dropoff' => $dropoffSegment ? $dropoffSegment->dropoff : null,
                'date'    => $departureSegment && $departureSegment->date ? $departureSegment->date : null,
                'time'    => $departureSegment && $departureSegment->time ? $departureSegment->time : null,
            ];
        });

        $ride_cancelled = false;
        $completed_date_time = Carbon::parse($ride->completed_date . ' ' . $ride->completed_time);
        if (isset($ride_booking) && ($completed_date_time < Carbon::now() || $ride_booking->status == '3' || $ride_booking->status == '4')) {
            $ride_cancelled = true;
        }

        // When "From" is a stop (not the route origin), show that segment's departure date/time
        $displayDepartureDate = $ride->date;
        $displayDepartureTime = $ride->time ?? null;
        if ($from !== null && $from !== '' && (string) $from !== (string) $origin) {
            $segmentFrom = $allDetails->first(function ($d) use ($from) {
                return (string) $d->departure === (string) $from;
            });
            if ($segmentFrom) {
                $displayDepartureDate = $segmentFrom->date ?? $ride->date;
                $displayDepartureTime = $segmentFrom->time ?? $ride->time;
            }
        }
        $displayDepartureDateTime = ($displayDepartureDate ?? '') . ' ' . ($displayDepartureTime ?? '00:00');

        $fromLabel = $from ?? null;
        $toLabel = $to ?? null;

        return view('ride_detail', [
            'fromLabel'        => $fromLabel,
            'toLabel'          => $toLabel,
            'ride_cancelled'   => $ride_cancelled,
            'rideDetailPage'   => $rideDetailPage,
            'ride'             => $ride,
            'setting'          => $setting,
            'cancelSetting'    => $cancelSetting,
            'postRidePage'     => $postRidePage,
            'ratings'          => $ratings,
            'chatsPage'        => $chatsPage,
            'origin'           => $origin,
            'destination'      => $destination,
            'stops'            => $stops,
            'segmentPickup'    => $segmentPickup,
            'segmentDropoff'   => $segmentDropoff,
            'searchFrom'               => $from,
            'searchTo'                 => $to,
            'displayDepartureDateTime' => $displayDepartureDateTime,
        ]);
    }

    public function MyCoPassengers(Request $request, $lang = null)
    {
        $ride = Ride::where('id', $request->id)->first();
        $setting = ReviewSetting::first();
        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'payment_methods_option3', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'luggage_option1', 'luggage_option2', 'luggage_option3', 'luggage_option4', 'luggage_option5', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('booking_option1', 'booking_option2', 'payment_methods_option1', 'payment_methods_option2', 'payment_methods_option3', 'smoking_option1', 'animals_option1', 'animals_option2', 'animals_option3', 'luggage_option1', 'luggage_option2', 'luggage_option3', 'luggage_option4', 'luggage_option5', 'features_option3', 'features_option4', 'features_option5', 'features_option6', 'features_option7', 'features_option8', 'features_option9', 'features_option10', 'features_option11', 'features_option12', 'features_option13', 'features_option14', 'features_option15', 'features_option16')->first();
            }
        }

        $notifications = null;
        if (auth()->user()) {
            $user_id = auth()->user()->id;
            $notifications = Notification::where('is_delete', '0')->where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is 2 and booking_id belongs to the user
                    $query->where('type', '2')
                        ->whereHas('booking', function ($query) use ($user_id) {
                            $query->where('user_id', $user_id);
                        });
                })
                ->orWhere(function ($query) use ($user_id) {
                    // Ratings where type is null and receiver_id belongs to the user
                    $query->where('type', null)
                        ->whereHas('receiver', function ($query) use ($user_id) {
                            $query->where('id', $user_id);
                        });
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        $ratings = Rating::all();
        return view('my_co_passengers', ['ride' => $ride, 'setting' => $setting, 'ratings' => $ratings, 'notifications' => $notifications, 'languages' => $languages, 'selectedLanguage' => $selectedLanguage, 'postRidePage' => $postRidePage]);
    }

    public function EditRide($lang, $id)
    {
        $ride = Ride::with(['defaultRideDetail', 'MoreRideDetail'])->where('id', $id)->first();

        if (!isset($ride) && empty($ride)) {
            $lang = $lang ?? "en";
            return redirect(route('home', ['lang' => $lang]));
        }

        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        // Ensure the ride's selected vehicle is in the list so it can be shown as selected
        if ($ride->vehicle_id) {
            $rideVehicle = Vehicle::where('id', $ride->vehicle_id)->where('user_id', $user_id)->first();
            if ($rideVehicle && $vehicles->where('id', $ride->vehicle_id)->isEmpty()) {
                $vehicles->push($rideVehicle);
            }
        }
        $rides = Ride::where('added_by', $user_id)->get();

        if ($rides->isNotEmpty()) {
            // Fetch ratings where the driver_id matches the authenticated user's ID
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

            if ($ratings->count() > 0) {
                // Calculate total average
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $languages = Language::all();
        // Store the selected language in the session
        if ($lang && in_array($lang, $languages->pluck('abbreviation')->toArray())) {
            session(['selectedLanguage' => $lang]);
        }
        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        $postRideSubDetailPage = null;
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();

            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                // Retrieve the HomePageSettingDetail associated with the selected language
                $postRidePage = $this->getPostRidePageWithSettingDetail();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $notificationPage = ChatsPageSettingDetail::where('language_id', $selectedLanguage->id)->select('notification_delete_text')->first();
                $successMessage = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('cancel_button', 'delete_button')->first();
                $postRidePage = $this->getPostRidePageWithSettingDetail();
            }
        }

        $notifications = Notification::where('is_delete', '0');
        $notifications = $notifications->where(function ($query) use ($user_id) {
            $query->where('type', '1')->whereHas('ride', function ($query) use ($user_id) {
                $query->where('added_by', $user_id);
            })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', '2')->whereHas('booking', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    });
                })
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('type', null)->whereHas('receiver', function ($query) use ($user_id) {
                        $query->where('id', $user_id);
                    });
                });
        })
            ->orderBy('id', 'desc')
            ->get();

        // Stops/origin/destination and segment data for edit form (moved from edit_ride.blade.php)
        $originText = isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0])
            ? $ride->defaultRideDetail[0]->departure
            : '';
        $destinationText = isset($ride->defaultRideDetail) && isset($ride->defaultRideDetail[0])
            ? $ride->defaultRideDetail[0]->destination
            : '';
        $stopsForDisplay = [];
        $pricesForDisplay = [];
        $segmentIdsForStops = [];
        $chainSegments = [];
        if (null !== old('stop_spot_display') && is_array(old('stop_spot_display'))) {
            $stopsForDisplay = old('stop_spot_display');
            $pricesForDisplay = null !== old('price_spot_display') && is_array(old('price_spot_display'))
                ? old('price_spot_display')
                : array_fill(0, count($stopsForDisplay), '');
        } elseif (null !== old('to_spot') && is_array(old('to_spot')) && count(old('to_spot')) > 0) {
            $toSpots = old('to_spot');
            $n = count($toSpots) - 1;
            for ($i = 0; $i < $n; $i++) {
                $stopsForDisplay[] = $toSpots[$i];
            }
            $pricesForDisplay = null !== old('price_spot') && is_array(old('price_spot'))
                ? array_slice(old('price_spot'), 0, $n)
                : array_fill(0, count($stopsForDisplay), '');
        } elseif (!empty($ride->moreRideDetail) && count($ride->moreRideDetail) > 0) {
            $details = $ride->moreRideDetail->sortBy('id')->values();
            $orderedPoints = collect([$originText]);
            $current = $originText;
            $remaining = $details;
            while ($current !== $destinationText && $remaining->isNotEmpty()) {
                $nextSegment = $remaining->first(function ($d) use ($current) {
                    return (string) $d->departure === (string) $current;
                });
                if (!$nextSegment) {
                    break;
                }
                $chainSegments[] = $nextSegment;
                $orderedPoints->push($nextSegment->destination);
                $current = $nextSegment->destination;
                $remaining = $remaining->filter(function ($d) use ($nextSegment) {
                    return $d->id != $nextSegment->id;
                });
            }
            $segmentIdsForStops = collect($chainSegments)->pluck('id')->values()->all();
            $chainStops = $orderedPoints->count() > 2
                ? $orderedPoints->slice(1, $orderedPoints->count() - 2)->values()
                : collect();
            foreach ($chainStops as $index => $stop) {
                $stopsForDisplay[] = $stop;
                $pricesForDisplay[] = isset($chainSegments[$index]) ? ($chainSegments[$index]->price ?? '') : '';
            }
        }
        $stopPickupDropoffForDisplay = [];
        if (null !== old('stop_pickup_dropoff') && is_array(old('stop_pickup_dropoff'))) {
            $stopPickupDropoffForDisplay = old('stop_pickup_dropoff');
        } elseif (!empty($ride->moreRideDetail) && count($ride->moreRideDetail) > 0 && !empty($chainSegments)) {
            foreach ($chainSegments as $index => $segment) {
                if ($index >= count($stopsForDisplay)) {
                    break;
                }
                $stopPickupDropoffForDisplay[] = $segment->dropoff ?? '';
            }
        }
        // Times per stop (for UI).
        // Prefer old('stop_time') when present.
        // Otherwise, use the arrival time at that stop:
        //   the segment whose destination matches this stop, from ride_details.destination_time.
        $stopTimesForDisplay = [];
        if (null !== old('stop_time') && is_array(old('stop_time'))) {
            $stopTimesForDisplay = old('stop_time');
        } elseif (!empty($chainSegments) && !empty($stopsForDisplay)) {
            foreach ($stopsForDisplay as $index => $stopName) {
                $timeForStop = '';
                foreach ($chainSegments as $seg) {
                    // When a stop matches a segment's departure ('from'), use that segment's departure time
                    if ((string) $seg->departure === (string) $stopName && !empty($seg->time)) {
                        $timeForStop = $seg->time;
                        break;
                    }
                }
                $stopTimesForDisplay[] = $timeForStop;
            }
        }
        if (empty($stopsForDisplay)) {
            $stopsForDisplay = [''];
            $pricesForDisplay = [''];
        }
        if (count($pricesForDisplay) !== count($stopsForDisplay)) {
            $pricesForDisplay = array_pad($pricesForDisplay, count($stopsForDisplay), '');
        }
        if (count($stopPickupDropoffForDisplay) !== count($stopsForDisplay)) {
            $stopPickupDropoffForDisplay = array_pad($stopPickupDropoffForDisplay, count($stopsForDisplay), '');
        }
        if (count($stopTimesForDisplay) !== count($stopsForDisplay)) {
            $stopTimesForDisplay = array_pad($stopTimesForDisplay, count($stopsForDisplay), '');
        }
        $stopDatesForDisplay = [];
        if (null !== old('stop_date') && is_array(old('stop_date'))) {
            $stopDatesForDisplay = old('stop_date');
        } elseif (!empty($chainSegments)) {
            foreach ($chainSegments as $seg) {
                $stopDatesForDisplay[] = $seg->date ? Carbon::parse($seg->date)->format('F d, Y') : '';
            }
        }
        if (count($stopDatesForDisplay) !== count($stopsForDisplay)) {
            $stopDatesForDisplay = array_pad($stopDatesForDisplay, count($stopsForDisplay), '');
        }
        $segmentsForPrice = [];
        $realStops = array_values(array_filter($stopsForDisplay, function ($s) {
            return trim((string) $s) !== '';
        }));
        if (count($realStops) > 0) {
            if (!empty($chainSegments)) {
                $n = count($chainSegments);
                for ($i = 0; $i < $n; $i++) {
                    $segmentsForPrice[] = [
                        'from' => $chainSegments[$i]->departure ?? '',
                        'to' => $chainSegments[$i]->destination ?? '',
                        'price' => $chainSegments[$i]->price ?? '',
                    ];
                }
            } elseif (null !== old('from_spot') && is_array(old('from_spot')) && null !== old('to_spot') && is_array(old('to_spot')) && count(old('from_spot')) > 0) {
                $fromSpot = old('from_spot');
                $toSpot = old('to_spot');
                $prices = null !== old('price_spot') && is_array(old('price_spot'))
                    ? old('price_spot')
                    : (null !== old('price_spot_display') && is_array(old('price_spot_display')) ? old('price_spot_display') : []);
                for ($i = 0; $i < count($fromSpot); $i++) {
                    $segmentsForPrice[] = [
                        'from' => $fromSpot[$i] ?? '',
                        'to' => $toSpot[$i] ?? '',
                        'price' => $prices[$i] ?? '',
                    ];
                }
            } else {
                $n = count($realStops);
                $pricesFromOld = null !== old('price_spot_display') && is_array(old('price_spot_display')) ? old('price_spot_display') : [];
                for ($i = 0; $i <= $n; $i++) {
                    $from = $i === 0 ? $originText : $realStops[$i - 1];
                    $to = $i === $n ? $destinationText : $realStops[$i];
                    $segmentsForPrice[] = [
                        'from' => $from,
                        'to' => $to,
                        'price' => $pricesFromOld[$i] ?? '',
                    ];
                }
            }
        }
        $hasStops = count($realStops) > 0;

        $vehicleTypes = $this->getVehicleTypesByLanguage();

        return view('edit_ride', [
            'notificationPage' => $notificationPage,
            'successMessage' => $successMessage,
            'postRidePage' => $postRidePage,
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'ride' => $ride,
            'user' => $user,
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'pinkRideSetting' => $pinkRideSetting,
            'setting' => $setting,
            'overallRating' => $overallRating,
            'notifications' => $notifications,
            'languages' => $languages,
            'selectedLanguage' => $selectedLanguage,
            'routeType' => 'edit',
            'originText' => $originText,
            'destinationText' => $destinationText,
            'stopsForDisplay' => $stopsForDisplay,
            'pricesForDisplay' => $pricesForDisplay,
            'segmentIdsForStops' => $segmentIdsForStops,
            'chainSegments' => $chainSegments,
            'stopPickupDropoffForDisplay' => $stopPickupDropoffForDisplay,
            'stopTimesForDisplay' => $stopTimesForDisplay,
            'stopDatesForDisplay' => $stopDatesForDisplay,
            'segmentsForPrice' => $segmentsForPrice,
            'realStops' => $realStops,
            'hasStops' => $hasStops,
        ]);
    }

    public function UpdateRide($lang, $ride_id, Request $request)
    {
        $ride = Ride::where('id', $ride_id)->first();
        $user = auth()->user();
        $user_id = $user->id;
        $rides = Ride::where('added_by', $user_id)->whereNotIn('id', [$ride_id])->get();
        $adminSetting = SiteSetting::first();

        // Check if ride has any bookings - if so, price cannot be changed
        $hasBookings = Booking::where('ride_id', $ride_id)
            ->where('status', '<>', 3)
            ->where('status', '<>', 4)
            ->whereHas('passenger', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->exists();

        // If bookings exist, check if price is being changed
        if ($hasBookings && $ride->defaultRideDetail && isset($ride->defaultRideDetail[0])) {
            $currentPrice = $ride->defaultRideDetail[0]->price;
            $newPrice = $request->price;

            if ($currentPrice != $newPrice) {
                return back()->with('error', 'You cannot change the price once passengers have booked this ride.')
                    ->with('heading', 'Price Change Not Allowed')
                    ->withInput();
            }
        }

        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'block_post_ride_message', 'overlap_ride_title', 'overlap_ride_message', 'ride_dead_time_text', 'profile_photo_required_message')->first();
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'block_post_ride_message', 'overlap_ride_title', 'overlap_ride_message', 'ride_dead_time_text', 'profile_photo_required_message')->first();
        }

        if ($user->block_post_ride == '1') {
            return $this->apiErrorResponse($message->block_post_ride_message ?? null, 200);
        }


        // Check if user has suspanded
        if ($user->suspand === '1') {
            return back()->with('message', $this->successMessage['admin_block_account_message'] ?? 'Your account has been suspended by the admin');
        }

        if (!isset($user->profile_image) || $user->profile_image == '' || in_array(basename($user->profile_image), ['male.png', 'female.png', 'neutral.png'])) {
            return back()->with('message', $message->profile_photo_required_message ?? 'For posting a ride profile photo is required');
        }

        $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
        $formattedTime = Carbon::createFromFormat('H:i', $request->time)->format('H:i:s');

        // Normalize stop_date[] to Y-m-d for consistent save to ride_details
        if ($request->has('stop_date') && !is_array($request->stop_date)) {
            $request->merge(['stop_date' => [$request->stop_date]]);
        }
        if ($request->has('stop_time') && !is_array($request->stop_time)) {
            $request->merge(['stop_time' => [$request->stop_time]]);
        }

        if ($request->has('stop_date') && is_array($request->stop_date)) {
            $normalizedStopDates = [];
            foreach ($request->stop_date as $idx => $v) {
                if ($v === null || trim((string) $v) === '') {
                    $normalizedStopDates[$idx] = '';
                    continue;
                }
                try {
                    $normalizedStopDates[$idx] = Carbon::parse($v)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $normalizedStopDates[$idx] = '';
                }
            }
            $request->merge(['stop_date' => $normalizedStopDates]);
        }

        if ($request->has('stop_time') && is_array($request->stop_time)) {
            $normalizedStopTimes = [];
            foreach ($request->stop_time as $idx => $v) {
                if ($v === null || trim((string) $v) === '') {
                    $normalizedStopTimes[$idx] = '';
                    continue;
                }
                try {
                    $normalizedStopTimes[$idx] = Carbon::parse($v)->format('H:i:s');
                } catch (\Throwable $e) {
                    $normalizedStopTimes[$idx] = '';
                }
            }
            $request->merge(['stop_time' => $normalizedStopTimes]);
        }

        $rides = DB::table('rides')
            ->where('status', '!=', 2)
            ->where('id', '!=', $ride->id)
            ->where('added_by', $user_id)
            ->where(function ($query) use ($formattedDate, $formattedTime) {
                $query->where('date', '<=', $formattedDate)
                    ->where('time', '<=', $formattedTime);
            })
            ->where(function ($query) use ($formattedDate, $formattedTime) {
                $query->where('destination_reached_date', '>=', $formattedDate)
                    ->where('destination_reached_time', '>=', $formattedTime);
            })
            ->first();

        if (isset($rides) && !empty($rides)) {
            $oldInput = $request->all();
            return back()->with('error', $message->overlap_ride_message ?? 'this ride overlaps with an existing ride you already have')->with('heading', $message->overlap_ride_title ?? 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
        }
        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);
        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return redirect()->back()
                ->with('error', $message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future')
                ->withInput();
        }
        $skip_vehicle = $request->filled('skip_vehicle') ? $request->skip_vehicle : 0;
        $add_vehicle = $request->filled('add_vehicle') ? $request->add_vehicle : 0;
        $added_vehicle = $request->filled('added_vehicle') ? $request->added_vehicle : 0;

        $recurring = $request->filled('recurring') ? $request->recurring : '0';

        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'pickup' => 'required',
            'dropoff' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'details' => 'required|string|max_words:300',
            'seats' => 'required|numeric|min:1',
            'smoke' => 'required',
            'animal_friendly' => 'required',
            'features' => 'array|min:1',
            'booking_method' => 'required',
            'luggage' => 'required',
            'price' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'notes' => 'nullable|string|max:300',
            'middle_seats' => 'required|numeric|min:1',
            'back_seats' => 'required|numeric|min:1',
            'agree_terms' => 'required',
            'image' => $request->has('existing_image') || $skip_vehicle !== 0 ? 'nullable|mimes:jpeg,png,jpg,gif|max:10240' : 'required|mimes:jpeg,png,jpg,gif|max:10240',
            'make' => $add_vehicle == 1 ? 'required' : 'nullable',
            'model' => $add_vehicle == 1 ? 'required' : 'nullable',
            'vehicle_type' => $add_vehicle == 1 ? 'required|integer|exists:features_setting_detail,features_setting_id' : 'nullable',
            'year' => $add_vehicle == 1 ? 'required' : 'nullable',
            'color' => $add_vehicle == 1 ? 'required' : 'nullable',
            'license_no' => $add_vehicle == 1 ? 'required' : 'nullable',
            'car_type' => $add_vehicle == 1 ? 'required' : 'nullable',
            'recurring_type' => $recurring !== '0' ? 'required' : 'nullable',
            'recurring_trips' => $recurring !== '0' ? 'required' : 'nullable',
        ], [
            'from.required' => 'The from is required',
            'to.required' => 'The to is required',
            'pickup.required' => 'The pickup is required',
            'dropoff.required' => 'The dropoff is required',
            'date.required' => 'The date is required',
            'date.date' => 'The date must be a valid date',
            'time.required' => 'The time is required',
            'time.date_format' => 'The time must be a valid time',
            'details.required' => 'The details is required',
            'details.string' => 'The details must be a string',
            'details.max_words' => 'The details may not be greater than 300 words',
            'seats.required' => 'The seats is required',
            'seats.numeric' => 'The seats must be a number',
            'seats.min' => 'The seats must be at least 1',
            'smoke.required' => 'The smoke is required',
            'animal_friendly.required' => 'The animal friendly is required',
            'features.array' => 'The features must be an array',
            'features.min' => 'The features must be at least 1',
            'booking_method.required' => 'The booking method is required',
            'luggage.required' => 'The luggage is required',
            'price.required' => 'The price is required',
            'price.numeric' => 'The price must be a number',
            'price.gt' => 'The price must be greater than 0',
            'payment_method.required' => 'The payment method is required',
            'notes.nullable' => 'The notes may be null',
            'notes.string' => 'The notes must be a string',
            'notes.max' => 'The notes may not be greater than 300 characters',
            'middle_seats.required' => 'The middle seats is required',
            'middle_seats.numeric' => 'The middle seats must be a number',
            'middle_seats.min' => 'The middle seats must be at least 1',
            'back_seats.required' => 'The back seats is required',
            'back_seats.numeric' => 'The back seats must be a number',
            'back_seats.min' => 'The back seats must be at least 1',
            'agree_terms.required' => 'The agree terms is required',
            'image.nullable' => 'The image may be null',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif',
            'image.max' => 'The image must be less than 10MB',
            'make.required' => 'The make is required',
            'model.required' => 'The model is required',
            'vehicle_type.required' => 'The vehicle type is required',
            'year.required' => 'The year is required',
            'color.required' => 'The color is required',
            'license_no.required' => 'The license no is required',
            'car_type.required' => 'The car type is required',
            'recurring_type.required' => 'The recurring type is required',
            'recurring_trips.required' => 'The recurring trips is required',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('/car_images');
            $file->move($destination_path, $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } elseif ($skip_vehicle !== 0) {
            $filename = '';
        }

        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;

        // Join the selected checkboxes with semicolons.
        $features = implode('=', $request->input('features', []));

        // Feature gatekeeping logic for Pink Ride and Extra Care Ride
        if ($request->has('features') && is_array($request->features)) {
            $pinkRideSetting = \App\Models\PinkRideSetting::first();
            $folkRideSetting = \App\Models\FolkRideSetting::first();
            $postRidePage = \App\Models\PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();

            // Get feature IDs for Pink Ride and Extra Care Ride
            $pinkRideFeatureId = $postRidePage->features_option1->features_setting_id ?? null;
            $extraCareFeatureId = $postRidePage->features_option2->features_setting_id ?? null;

            // Check if Pink Ride feature is selected
            if ($pinkRideFeatureId && in_array($pinkRideFeatureId, $request->features)) {
                // GENDER VALIDATION: Only female users can post Pink Rides
                if ($pinkRideSetting && $pinkRideSetting->female === '1') {
                    // Check if user has admin override (pink_ride = '1')
                    if ($user->pink_ride !== '1') {
                        // If user is explicitly disabled (pink_ride = '0'), block them
                        if ($user->pink_ride === '0') {
                            return back()->with('message', 'You are not allowed to post Pink Rides. Please contact support if you believe this is an error.');
                        }
                        // If pink_ride is empty/null, check gender restriction
                        if ($user->gender !== 'female') {
                            return back()->with('message', 'Only female drivers can post Pink Rides.');
                        }
                    }
                }

                // Check if driver's license is required and uploaded
                if ($pinkRideSetting && $pinkRideSetting->driver_license === '1') {
                    if (empty($user->driver_license_upload)) {
                        return back()->with('message', 'A government-issued photo ID (driver\'s license) is required to post Pink Rides. Please upload your driver\'s license in your profile.');
                    }
                }
            }

            // Check if Extra Care Ride feature is selected
            if ($extraCareFeatureId && is_array($request->features) && in_array($extraCareFeatureId, $request->features)) {
                $extraCareError = $this->validateExtraCareEligibility($user);
                if ($extraCareError) {
                    return back()->with('message', $extraCareError);
                }
            }
        }

        // Initialize vehicle variables to prevent undefined variable errors
        $make = '';
        $model = '';
        $vehicle_type = '';
        $year = '';
        $color = '';
        $license_no = '';
        $car_type = '';

        if ($skip_vehicle !== 0) {
            $make = '';
            $model = '';
            $vehicle_type = '';
            $year = '';
            $color = '';
            $license_no = '';
            $car_type = '';
        }

        if ($add_vehicle !== 0) {
            // Preserve original values if request values are empty (for edit mode when fields are readonly/disabled)
            $make = $request->make ?: $ride->make ?? '';
            $model = $request->model ?: $ride->model ?? '';
            $vehicle_type = Ride::normalizeRideVehicleTypeId($request->vehicle_type ?: $ride->vehicle_type ?? '');
            $year = $request->year ?: $ride->year ?? '';
            $color = $request->color ?: $ride->color ?? '';
            $license_no = $request->license_no ?: $ride->license_no ?? '';
            $car_type = $request->car_type ?: $ride->car_type ?? '';

            // Create new vehicle with the values (preserved from original if request was empty)
            if ($make || $model || $vehicle_type) {
                $vehicle = Vehicle::create([
                    'user_id' => auth()->user()->id,
                    'make' => $make,
                    'model' => $model,
                    'type' => Vehicle::normalizeVehicleTypeId($vehicle_type),
                    'liscense_no' => $license_no,
                    'color' => $color,
                    'year' => $year,
                    'car_type' => $car_type,
                    'image' => $filename,
                    'original_image' => $filename,
                ]);
                $vehicle_id = $vehicle->id;
            }
        }

        if ($added_vehicle !== 0) {
            $vehicle = Vehicle::whereId($request->vehicle_id)->first();
            if ($vehicle) {
                $make = $vehicle->make;
                $model = $vehicle->model;
                $vehicle_type = $vehicle->type;
                $year = $vehicle->year;
                $color = $vehicle->color;
                $license_no = $vehicle->liscense_no;
                $car_type = $vehicle->car_type;
                $vehicle_id = $vehicle->id;
                if ($vehicle->remove_image === '0') {
                    $imageName = basename($vehicle->image);
                    $filename = $imageName;
                } else {
                    $filename = '';
                }
            } else {
                $make = '';
                $model = '';
                $vehicle_type = '';
                $year = '';
                $color = '';
                $license_no = '';
                $car_type = '';
            }
        }

        // When no vehicle option is selected (all 0), keep existing ride vehicle data
        if ($skip_vehicle == 0 && $add_vehicle == 0 && $added_vehicle == 0) {
            $vehicle_id = $ride->vehicle_id;
            $make = $ride->make ?? '';
            $model = $ride->model ?? '';
            $vehicle_type = $ride->vehicle_type ?? '';
            $year = $ride->year ?? '';
            $color = $ride->color ?? '';
            $license_no = $ride->license_no ?? '';
            $car_type = $ride->car_type ?? '';
            if (!isset($filename)) {
                $filename = $ride->getRawOriginal('car_image') ?? $ride->getRawOriginal('car_image_original') ?? '';
            }
        }

        if ($recurring == '1') {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        } else {
            $recurring_type = '';
            $recurring_trips = '';
        }

        $ride->update([
            'departure' => "",
            'departure_lat' => '',
            'departure_lng' => '',
            'departure_place' => '',
            'departure_route' => '',
            'departure_zipcode' => '',
            'departure_city' => '',
            'departure_state' => '',
            'departure_state_short' => '',
            'departure_country' => '',

            'destination' => "",
            'destination_lat' => '',
            'destination_lng' => '',
            'destination_place' => '',
            'destination_route' => '',
            'destination_zipcode' => '',
            'destination_city' => '',
            'destination_state' => '',
            'destination_state_short' => '',
            'destination_country' => '',

            'total_distance' => "",
            'total_time' => "",
            'date' => Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d'),
            'time' => $request->time,

            'recurring' => $recurring,
            'recurring_type' => $recurring_type,
            'recurring_trips' => $recurring_trips,
            'details' => $request->details,
            'seats' => $request->seats,

            'skip_vehicle' => $skip_vehicle,
            'add_vehicle' => $add_vehicle,
            'added_vehicle' => $added_vehicle,
            'vehicle_id' => $vehicle_id ?? null,
            'make' => $make,
            'model' => $model,
            'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
            'year' => $year,
            'color' => $color,
            'license_no' => $license_no,
            'car_type' => $car_type,
            'car_image' => $filename,
            'car_image_original' => $filename,
            'smoke' => $request->smoke,
            'animal_friendly' => $request->animal_friendly,
            'features' => $features,
            'booking_method' => $request->booking_method,
            'booking_type' => $request->booking_type,
            'max_back_seats' => $max_back_seats,
            'luggage' => $request->luggage,
            'accept_more_luggage' => $accept_more_luggage,
            'open_customized' => $open_customized,
            'price' => "",
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'added_by' => $user_id,
            'until_date' => null,
            'until_limit' => '',

            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,

            'middle_seats' => $request->middle_seats,
            'back_seats' => $request->back_seats,
        ]);

        $getSeatDetails = SeatDetail::where('ride_id', $ride->id)->get();
        foreach ($getSeatDetails as $key => $getSeatDetail) {
            $getSeatDetail->delete();
        }

        for ($i = 1; $i <= $ride->seats; $i++) {
            $seatDetail = new SeatDetail;
            $seatDetail->ride_id = $ride->id;
            $seatDetail->seat_number = $i;
            $seatDetail->status = 'pending';
            $seatDetail->save();
        }

        //Second - Get distance and duration from Google Maps API
        $duration = 0;
        $distance = 0;
        // Use original from/to values - getDataFromGoogleApi will properly encode them
        $from = $request->from;
        $to = $request->to;
        $fromArray = explode(',', $request->from);
        $toArray = explode(',', $request->to);

        Log::info('Calculating distance for ride update', [
            'ride_id' => $ride->id,
            'from' => $from,
            'to' => $to,
            'user_id' => $user_id
        ]);

        $googleApiData = $this->getDataFromGoogleApi($from, $to);
        if (isset($googleApiData) && !empty($googleApiData)) {
            // Check element status first before accessing distance/duration
            $elementStatus = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['status']) ? $googleApiData['rows'][0]['elements'][0]['status'] : null;

            if ($elementStatus === 'OK') {
                $duration = isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;
                $distance = isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
            } else {
                // Log element status when it's not 'OK' for debugging
                Log::warning('Google Maps API element status is not OK for ride update', [
                    'ride_id' => $ride->id,
                    'from' => $from,
                    'to' => $to,
                    'element_status' => $elementStatus,
                    'api_status' => $googleApiData['status'] ?? 'unknown',
                    'error_message' => $googleApiData['error_message'] ?? 'No error message',
                    'full_response' => $googleApiData
                ]);
                $duration = 0;
                $distance = 0;
            }
        }

        if ($distance != 0) {
            $distance = round(($distance / 1000), 2);
        }

        Log::info('Distance calculation completed for ride update', [
            'ride_id' => $ride->id,
            'from' => $from,
            'to' => $to,
            'distance_km' => $distance,
            'duration_seconds' => $duration,
            'distance_meters' => $distance * 1000
        ]);

        if (isset($request->default_ride_detail_id)) {
            $rideDetail = RideDetail::where('id', $request->default_ride_detail_id)->first();
        } else {
            $rideDetail = new RideDetail();
        }

        $rideDetail->ride_id = $ride->id;
        $rideDetail->departure = $request->from;
        $rideDetail->destination = $request->to;
        $rideDetail->pickup = $request->pickup ?? null;
        $rideDetail->dropoff = $request->dropoff ?? null;
        $rideDetail->default_ride = 1;
        $rideDetail->total_distance = $distance;
        $rideDetail->total_duration = $duration;

        Log::info('Saving ride detail with distance', [
            'ride_id' => $ride->id,
            'ride_detail_id' => $rideDetail->id ?? 'new',
            'departure' => $request->from,
            'destination' => $request->to,
            'total_distance_km' => $distance,
            'total_duration_seconds' => $duration
        ]);

        // Cost-sharing cap validation: Price per seat validation
        // Formula: (Distance × Cap) ÷ Seats = Max price per seat
        // Skip validation if user explicitly chose to bypass (after seeing warning)
        $bypassValidation = $request->has('bypass_price_validation') && $request->bypass_price_validation == '1';

        if (!$bypassValidation && $distance > 0 && isset($request->price) && $request->price > 0 && isset($request->seats) && $request->seats > 0) {
            $seats = (int)$request->seats;
            $pricePerSeat = (float)$request->price;

            // Calculate max allowed price per seat using Error-Triggering Cap: $0.72/km
            $maxPricePerSeat = ($distance * 0.72) / $seats;

            // Calculate soft warning price per seat: $0.66/km
            $softWarningPricePerSeat = ($distance * 0.66) / $seats;

            Log::info('Price per seat calculation (UpdateRide)', [
                'ride_id' => $ride->id,
                'price_per_seat' => $pricePerSeat,
                'distance_km' => $distance,
                'seats' => $seats,
                'max_price_per_seat' => round($maxPricePerSeat, 2),
                'soft_warning_price_per_seat' => round($softWarningPricePerSeat, 2),
                'error_cap' => 0.72,
                'warning_cap' => 0.66
            ]);

            // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
            if ($pricePerSeat > $maxPricePerSeat) {
                Log::warning('Price per seat exceeds error-triggering cap (UpdateRide)', [
                    'ride_id' => $ride->id,
                    'price_per_seat' => $pricePerSeat,
                    'max_allowed' => round($maxPricePerSeat, 2),
                    'cap' => 0.72
                ]);

                return back()->with('error', 'The price per seat ($' . number_format($pricePerSeat, 2) . ') exceeds the maximum allowed for cost-sharing rides ($' . number_format($maxPricePerSeat, 2) . ' per seat). Please adjust your price.')
                    ->with('heading', 'Price Limit Exceeded')
                    ->with('max_price_per_seat', round($maxPricePerSeat, 2))
                    ->withInput();
            }

            // Soft Warning Cap: $0.66 per km - WARN but ALLOW
            if ($pricePerSeat > $softWarningPricePerSeat) {
                Log::info('Price per seat exceeds soft warning cap but within error cap (UpdateRide)', [
                    'ride_id' => $ride->id,
                    'price_per_seat' => $pricePerSeat,
                    'soft_warning_price' => round($softWarningPricePerSeat, 2),
                    'warning_cap' => 0.66
                ]);

                // Return back to form with warning - user will see modal and can choose to proceed or adjust
                return back()->with('price_warning', [
                    'message' => 'The price you entered is above the standard reimbursement rate recommended by the CRA and Revenu Québec.',
                    'price_per_seat' => $pricePerSeat,
                    'soft_warning_price' => round($softWarningPricePerSeat, 2)
                ])->withInput();
            }
        }

        $rideDetail->price = $request->price;
        $rideDetail->time = $request->time;
        $rideDetail->date = Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');

        if (isset($adminSetting)) {

            if (isset($ride->date) && isset($ride->time)) {
                $rideDateTime = Carbon::parse("$ride->date $ride->time");
                $apiTime = 0;
                if ($duration != 0) {
                    $apiTime = round(($duration / 3600), 2);
                }

                // $rideDateTime->addHours($adminSetting->destination_hours);
                // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                $totalHours = $duration / 3600;
                $fullHours = floor($totalHours);
                $minutes = round(($totalHours - $fullHours) * 60);
                $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                    ->addMinutes($minutes);
                $destinationReachedDate = $rideDateTime->toDateString();
                $destinationReachedTime = $rideDateTime->toTimeString();


                $rideDateTime->addHours($adminSetting->ride_completed_hours);
                $completedDate = $rideDateTime->toDateString();
                $completedTime = $rideDateTime->toTimeString();

                $ride->completed_date = $completedDate ?? '';
                $ride->completed_time = $completedTime;
                $ride->destination_reached_date = $destinationReachedDate;
                $ride->destination_reached_time = $destinationReachedTime;
                $ride->save();

                $rideDetail->destination_time = $destinationReachedTime;
                $rideDetail->destination_date = $destinationReachedDate;
                $rideDetail->completed_time = $completedTime;
                $rideDetail->completed_date = $completedDate;
            }
        }
        $rideDetail->save();

        // Remove extra ride details that were removed from the form (so DB matches submitted rows)
        $submittedExtraIds = array_filter((array) $request->input('ride_detail_ids', []), function ($id) {
            return isset($id) && $id !== '' && $id !== '0';
        });
        RideDetail::where('ride_id', $ride->id)
            ->where('default_ride', 0)
            ->whereNotIn('id', $submittedExtraIds)
            ->delete();
        // If JS did not build from_spot[] / to_spot[] but stop_spot_display[] exists, build them server-side
        if ((!$request->has('from_spot') || empty($request->from_spot))
            && $request->has('stop_spot_display')
            && is_array($request->stop_spot_display)
        ) {
            $stops = array_values(array_filter($request->stop_spot_display, function ($v) {
                return trim((string) $v) !== '';
            }));
            if (!empty($stops) && $request->filled('from') && $request->filled('to')) {
                $origin = $request->from;
                $destination = $request->to;
                $fromSpot = [];
                $toSpot = [];
                $priceSpot = [];
                $mainPrice = $request->price;
                $segmentPrices = (array) $request->input('price_spot_display', []);
                $n = count($stops);
                for ($i = 0; $i <= $n; $i++) {
                    $fromVal = ($i === 0) ? $origin : ($stops[$i - 1] ?? null);
                    $toVal = ($i === $n) ? $destination : ($stops[$i] ?? null);
                    if (!$fromVal || !$toVal) {
                        continue;
                    }
                    $segPrice = $mainPrice;
                    if (isset($segmentPrices[$i]) && $segmentPrices[$i] !== '') {
                        $segPrice = $segmentPrices[$i];
                    }
                    $fromSpot[] = $fromVal;
                    $toSpot[] = $toVal;
                    $priceSpot[] = $segPrice;
                }
                if (!empty($fromSpot)) {
                    $request->merge([
                        'from_spot' => $fromSpot,
                        'to_spot' => $toSpot,
                        'price_spot' => $priceSpot,
                    ]);
                }
            }
        }

        if (isset($request->from_spot) && !empty($request->from_spot)) {
            $pointsForPairs = [$request->from];
            $validSegmentPrices = [];
            $validSegmentDistances = [];
            $validSegmentDurations = [];
            foreach ($request->from_spot as $key => $from_spot) {
                if (
                    empty($request->from_spot[$key]) ||
                    empty($request->to_spot[$key])
                ) {
                    continue;
                }
                $duration = 0;
                $distance = 0;
                $fromArray = explode(',', $request->from_spot[$key]);
                $toArray = explode(',', $request->to_spot[$key]);
                $googleApiData = $this->getDataFromGoogleApi($request->from_spot[$key], $request->to_spot[$key]);
                if (isset($googleApiData) && !empty($googleApiData)) {
                    // Check element status first before accessing distance/duration
                    $elementStatus = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['status']) ? $googleApiData['rows'][0]['elements'][0]['status'] : null;

                    if ($elementStatus === 'OK') {
                        $duration = isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;
                        $distance = isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
                    } else {
                        // Log element status when it's not 'OK' for debugging
                        Log::warning('Google Maps API element status is not OK for ride update spot', [
                            'ride_id' => $ride->id,
                            'spot_index' => $key,
                            'from' => $request->from_spot[$key],
                            'to' => $request->to_spot[$key],
                            'element_status' => $elementStatus,
                            'api_status' => $googleApiData['status'] ?? 'unknown',
                            'error_message' => $googleApiData['error_message'] ?? 'No error message',
                            'full_response' => $googleApiData
                        ]);
                        $duration = 0;
                        $distance = 0;
                    }
                }

                if ($distance != 0) {
                    $distance = round(($distance / 1000), 2);
                }

                $pointsForPairs[] = $request->to_spot[$key];
                $validSegmentPrices[] = $request->price_spot[$key] ?? 0;
                $validSegmentDistances[] = $distance;
                $validSegmentDurations[] = $duration;

                if (isset($request->ride_detail_ids) && isset($request->ride_detail_ids[$key]) && $request->ride_detail_ids[$key] != "0") {
                    $rideDetail = RideDetail::where('id', $request->ride_detail_ids[$key])->first();
                } else {
                    $rideDetail = new RideDetail();
                }
                $rideDetail->ride_id = $ride->id;
                $rideDetail->departure = $request->from_spot[$key];
                $rideDetail->destination = $request->to_spot[$key];
                $pickupNote = $key === 0
                    ? ($request->pickup ?? null)
                    : (isset($request->stop_pickup_dropoff[$key - 1]) ? trim((string) $request->stop_pickup_dropoff[$key - 1]) : null);
                $dropoffNote = ($key === count($request->from_spot) - 1)
                    ? ($request->dropoff ?? null)
                    : (isset($request->stop_pickup_dropoff[$key]) ? trim((string) $request->stop_pickup_dropoff[$key]) : null);
                $rideDetail->pickup = ($pickupNote !== '' && $pickupNote !== null) ? $pickupNote : ($request->pickup ?? null);
                $rideDetail->dropoff = ($dropoffNote !== '' && $dropoffNote !== null) ? $dropoffNote : ($request->dropoff ?? null);
                $rideDetail->default_ride = 0;
                $rideDetail->total_distance = $distance;
                $rideDetail->total_duration = $duration;
                $rideDetail->price = $request->price_spot[$key];
                // Segment departure time: origin segment uses main time; later segments use stop_time[ key - 1 ]
                $segmentTime = ($key > 0 && $request->has('stop_time') && is_array($request->stop_time) && isset($request->stop_time[$key - 1]) && (string) $request->stop_time[$key - 1] !== '')
                    ? $request->stop_time[$key - 1]
                    : $formattedTime;
                if (strlen($segmentTime) <= 5 && $segmentTime !== '') {
                    try {
                        $segmentTime = Carbon::createFromFormat('H:i', $segmentTime)->format('H:i:s');
                    } catch (\Throwable $e) {
                    }
                }
                $rideDetail->time = $segmentTime;
                $segmentDate = ($key > 0 && $request->has('stop_date') && is_array($request->stop_date) && isset($request->stop_date[$key - 1]) && (string) $request->stop_date[$key - 1] !== '')
                    ? $request->stop_date[$key - 1]
                    : Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
                $rideDetail->date = $segmentDate;

                if (isset($adminSetting)) {

                    if (isset($ride->date) && isset($ride->time)) {
                        $rideDateTime = Carbon::parse("$ride->date $ride->time");
                        $apiTime = 0;
                        if ($duration != 0) {
                            $apiTime = round(($duration / 3600), 2);
                        }

                        // $rideDateTime->addHours($adminSetting->destination_hours);
                        // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                        $totalHours = $duration / 3600;  // e.g., 109800 seconds → 30.5 hours
                        $fullHours = floor($totalHours);  // 30 hours
                        $minutes = round(($totalHours - $fullHours) * 60);  // 30 minutes
                        $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                            ->addMinutes($minutes);
                        $destinationReachedDate = $rideDateTime->toDateString();
                        $destinationReachedTime = $rideDateTime->toTimeString();


                        $rideDateTime->addHours($adminSetting->ride_completed_hours);
                        $completedDate = $rideDateTime->toDateString();
                        $completedTime = $rideDateTime->toTimeString();

                        $rideDetail->destination_time = $destinationReachedTime;
                        $rideDetail->destination_date = $destinationReachedDate;
                        $rideDetail->completed_time = $completedTime;
                        $rideDetail->completed_date = $completedDate;
                    }
                }
                $rideDetail->save();
            }

            // Create RideDetails for every (origin, destination) pair so the ride appears in all segment searches
            $numPoints = count($pointsForPairs);
            for ($i = 0; $i < $numPoints; $i++) {
                for ($j = $i + 2; $j < $numPoints; $j++) {
                    // Skip full route (origin to destination); it is already the default RideDetail
                    if ($i === 0 && $j === $numPoints - 1) {
                        continue;
                    }
                    $compositePrice = 0;
                    $compositeDistance = 0;
                    $compositeDuration = 0;
                    for ($k = $i; $k < $j; $k++) {
                        $compositePrice += $validSegmentPrices[$k] ?? 0;
                        $compositeDistance += $validSegmentDistances[$k] ?? 0;
                        $compositeDuration += $validSegmentDurations[$k] ?? 0;
                    }
                    $compositeDetail = new RideDetail();
                    $compositeDetail->ride_id = $ride->id;
                    $compositeDetail->departure = $pointsForPairs[$i];
                    $compositeDetail->destination = $pointsForPairs[$j];
                    $pickupNote = $i === 0
                        ? ($request->pickup ?? null)
                        : (isset($request->stop_pickup_dropoff[$i - 1]) ? trim((string) $request->stop_pickup_dropoff[$i - 1]) : null);
                    $dropoffNote = $j === $numPoints - 1
                        ? ($request->dropoff ?? null)
                        : (isset($request->stop_pickup_dropoff[$j - 1]) ? trim((string) $request->stop_pickup_dropoff[$j - 1]) : null);
                    $compositeDetail->pickup = ($pickupNote !== '' && $pickupNote !== null) ? $pickupNote : ($request->pickup ?? null);
                    $compositeDetail->dropoff = ($dropoffNote !== '' && $dropoffNote !== null) ? $dropoffNote : ($request->dropoff ?? null);
                    $compositeDetail->default_ride = 0;
                    $compositeDetail->total_distance = $compositeDistance;
                    $compositeDetail->total_duration = $compositeDuration;
                    $compositeDetail->price = $compositePrice;
                    $compositeSegmentTime = ($i > 0 && $request->has('stop_time') && is_array($request->stop_time) && isset($request->stop_time[$i - 1]) && (string) $request->stop_time[$i - 1] !== '')
                        ? $request->stop_time[$i - 1]
                        : $formattedTime;
                    if (strlen($compositeSegmentTime) <= 5 && $compositeSegmentTime !== '') {
                        try {
                            $compositeSegmentTime = Carbon::createFromFormat('H:i', $compositeSegmentTime)->format('H:i:s');
                        } catch (\Throwable $e) {
                        }
                    }
                    $compositeDetail->time = $compositeSegmentTime;
                    $compositeSegmentDate = ($i > 0 && $request->has('stop_date') && is_array($request->stop_date) && isset($request->stop_date[$i - 1]) && (string) $request->stop_date[$i - 1] !== '')
                        ? $request->stop_date[$i - 1]
                        : Carbon::createFromFormat('F d, Y', $request->date)->format('Y-m-d');
                    $compositeDetail->date = $compositeSegmentDate;
                    if (isset($adminSetting) && isset($ride->date) && isset($ride->time)) {
                        $cumulativeDurationToJ = 0;
                        for ($k = 0; $k < $j; $k++) {
                            $cumulativeDurationToJ += $validSegmentDurations[$k] ?? 0;
                        }
                        $rideDateTime = Carbon::parse("$ride->date $ride->time");
                        $totalHours = $cumulativeDurationToJ / 3600;
                        $fullHours = floor($totalHours);
                        $minutes = round(($totalHours - $fullHours) * 60);
                        $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)->addMinutes($minutes);
                        $compositeDetail->destination_time = $rideDateTime->toTimeString();
                        $compositeDetail->destination_date = $rideDateTime->toDateString();
                        $rideDateTime->addHours($adminSetting->ride_completed_hours);
                        $compositeDetail->completed_time = $rideDateTime->toTimeString();
                        $compositeDetail->completed_date = $rideDateTime->toDateString();
                    }
                    $compositeDetail->save();
                }
            }
        }

        // Check if the ride is recurring
        if ($recurring == '1') {
            // Determine the frequency and number of recurring trips
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = $request->input('recurring_trips');

            // Calculate the date interval based on the frequency
            $dateInterval = ($frequency === 'Daily') ? 'P1D' : 'P7D';

            $existingRecurringRides = Ride::where('recurring_id', $ride_id)->get();
            $initialRide = Ride::where('id', $ride_id)->first();

            // Create additional rides based on the recurring settings
            for ($i = 1; $i <= $numRecurringTrips; $i++) {
                $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                if (isset($existingRecurringRides[$i - 1])) {
                    // Update existing recurring ride
                    $initialRide = $existingRecurringRides[$i - 1]->update([
                        'departure' => "",
                        'destination' => "",
                        'date' => $nextDate->format('Y-m-d'),
                        'time' => $request->time,
                        'completed_date' => $nextCompletedDate->format('Y-m-d'),
                        'completed_time' => $initialRide->completed_time,
                        'destination_reached_date' => $nextDestinationReachedDate->format('Y-m-d'),
                        'destination_reached_time' => $initialRide->destination_reached_time,
                        'recurring' => $recurring,
                        'details' => $request->details,
                        'seats' => $request->seats,

                        'skip_vehicle' => $skip_vehicle,
                        'add_vehicle' => $add_vehicle,
                        'added_vehicle' => $added_vehicle,
                        'vehicle_id' => $vehicle_id ?? null,
                        'make' => $make,
                        'model' => $model,
                        'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
                        'year' => $year,
                        'color' => $color,
                        'license_no' => $license_no,
                        'car_type' => $car_type,
                        'car_image' => $filename,
                        'car_image_original' => $filename,
                        'smoke' => $request->smoke,
                        'animal_friendly' => $request->animal_friendly,
                        'features' => $features,
                        'booking_method' => $request->booking_method,
                        'booking_type' => $request->booking_type,
                        'max_back_seats' => $max_back_seats,
                        'luggage' => $request->luggage,
                        'accept_more_luggage' => $accept_more_luggage,
                        'open_customized' => $open_customized,
                        'price' => "",
                        'payment_method' => $request->payment_method,
                        'notes' => $request->notes,
                        'pickup' => $request->pickup,
                        'dropoff' => $request->dropoff,
                        'middle_seats' => $request->middle_seats,
                        'back_seats' => $request->back_seats,
                    ]);

                    $getSeatDetails = SeatDetail::where('ride_id', $initialRide->id)->get();
                    foreach ($getSeatDetails as $key => $getSeatDetail) {
                        $getSeatDetail->delete();
                    }


                    for ($j = 1; $j <= $initialRide->seats; $j++) {
                        $seatDetail = new SeatDetail;
                        $seatDetail->ride_id = $initialRide->id;
                        $seatDetail->seat_number = $j;
                        $seatDetail->status = 'pending';
                        $seatDetail->save();
                    }

                    $getRideDetails = RideDetail::where('ride_id', $initialRide->id)->get();
                    foreach ($getRideDetails as $key => $getRideDetail) {
                        $getRideDetail->delete();
                    }
                } else {
                    // Create new recurring ride
                    $initialRide = Ride::create([
                        'departure' => "",
                        'departure_lat' => '',
                        'departure_lng' => '',
                        'departure_place' => '',
                        'departure_route' => '',
                        'departure_zipcode' => '',
                        'departure_city' => '',
                        'departure_state' => '',
                        'departure_state_short' => '',
                        'departure_country' => '',

                        'destination' => "",
                        'destination_lat' => '',
                        'destination_lng' => '',
                        'destination_place' => '',
                        'destination_route' => '',
                        'destination_zipcode' => '',
                        'destination_city' => '',
                        'destination_state' => '',
                        'destination_state_short' => '',
                        'destination_country' => '',

                        'total_distance' => '',
                        'total_time' => '',
                        'date' => $nextDate->format('Y-m-d'),
                        'time' => $request->time,

                        'recurring' => '1',
                        'recurring_type' => '',
                        'recurring_trips' => '',
                        'recurring_id' => $ride_id,
                        'details' => $request->details,
                        'seats' => $request->seats,

                        'skip_vehicle' => $skip_vehicle,
                        'add_vehicle' => $add_vehicle,
                        'added_vehicle' => $added_vehicle,
                        'make' => $make,
                        'model' => $model,
                        'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
                        'year' => $year,
                        'color' => $color,
                        'license_no' => $license_no,
                        'car_type' => $car_type,
                        'car_image' => $filename,
                        'car_image_original' => $filename,
                        'smoke' => $request->smoke,
                        'animal_friendly' => $request->animal_friendly,
                        'features' => $features,
                        'booking_method' => $request->booking_method,
                        'booking_type' => $request->booking_type,
                        'max_back_seats' => $max_back_seats,
                        'luggage' => $request->luggage,
                        'accept_more_luggage' => $accept_more_luggage,
                        'open_customized' => $open_customized,
                        'price' => "",
                        'payment_method' => $request->payment_method,
                        'notes' => $request->notes,
                        'added_by' => $user_id,
                        'until_date' => null,
                        'until_limit' => '',

                        'pickup' => $request->pickup,
                        'dropoff' => $request->dropoff,

                        'middle_seats' => $request->middle_seats,
                        'back_seats' => $request->back_seats,
                        'added_on' => now(),
                    ]);


                    for ($j = 1; $j <= $initialRide->seats; $j++) {
                        $seatDetail = new SeatDetail;
                        $seatDetail->ride_id = $initialRide->id;
                        $seatDetail->seat_number = $j;
                        $seatDetail->status = 'pending';
                        $seatDetail->save();
                    }
                }

                $getRideDetails = RideDetail::where('ride_id', $initialRide->id)->get();
                foreach ($getRideDetails as $key => $getRideDetail) {
                    $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                    $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                    $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                    $rideDetail = new RideDetail();
                    $rideDetail->ride_id = $initialRide->id;
                    $rideDetail->departure = $getRideDetail->departure;
                    $rideDetail->destination = $getRideDetail->destination;
                    $rideDetail->pickup = $getRideDetail->pickup ?? null;
                    $rideDetail->dropoff = $getRideDetail->dropoff ?? null;
                    $rideDetail->default_ride = $getRideDetail->default_ride;
                    $rideDetail->total_distance = $getRideDetail->total_distance;
                    $rideDetail->total_duration = $getRideDetail->total_duration;
                    $rideDetail->price = $getRideDetail->price;
                    $rideDetail->time = $getRideDetail->time;
                    $rideDetail->date = $nextDate;
                    $rideDetail->destination_time = $getRideDetail->destination_time;
                    $rideDetail->destination_date = $nextDestinationReachedDate;
                    $rideDetail->completed_time = $getRideDetail->completed_time;
                    $rideDetail->completed_date = $nextCompletedDate;
                    $rideDetail->save();
                }
            }

            // Remove any excess rides
            for ($i = $numRecurringTrips; $i < count($existingRecurringRides); $i++) {
                $existingRecurringRides[$i]->delete();

                $rideId = $existingRecurringRides[$i]->id;

                $getRideDetails = RideDetail::where('ride_id', $rideId)->get();
                foreach ($getRideDetails as $key => $getRideDetail) {
                    $getRideDetail->delete();
                }
            }
        }

        return redirect()->route('my_rides', ['lang' => $selectedLanguage->abbreviation]);
        // return redirect()->route('my_ride_detail', ['lang' => $selectedLanguage->abbreviation, 'departure' => $ride->defaultRideDetail[0]->departure, 'destination' => $ride->defaultRideDetail[0]->destination, 'id' => $ride->id]);
    }

    public function CopyRide($lang, $id)
    {
        $ride = Ride::with(['defaultRideDetail', 'MoreRideDetail'])->where('id', $id)->first();
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();
        $noshows = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

        if ($rides->isNotEmpty()) {
            // Fetch ratings where the driver_id matches the authenticated user's ID
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

            if ($ratings->count() > 0) {
                // Calculate total average
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

       
                
        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $postRidePage = $this->getPostRidePageWithSettingDetail();
        

        $isNewForm = false;
        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $vehicleTypes = $this->getVehicleTypesByLanguage();
        $noShowsCount = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $cancellationCount = CancellationHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();
        return view('post_ride', ['postRideSubDetailPage' => $postRideSubDetailPage, 
        'postRidePage' => $postRidePage, 
        'vehicleTypes' => $vehicleTypes, 
        'vehiclePage' => $vehiclePage, 
        'cancellationCount' => $cancellationCount, 
        'noShowsCount' => $noShowsCount, 'isNewForm' => $isNewForm, 'ride' => $ride, 
        'noshows' => $noshows, 'user' => $user, 'vehicles' => $vehicles, 
        'pinkRideSetting' => $pinkRideSetting, 'setting' => $setting, 
        'overallRating' => $overallRating, 
        'routeType' => 'copy']);
    }

    public function RepostRide($lang, $id)
    {
        $ride = Ride::with(['defaultRideDetail', 'MoreRideDetail'])->where('id', $id)->first();
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();

        $noshows = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

        if ($ride) {
            // Swap departure and destination (From and To)
            $temp = $ride->defaultRideDetail[0]->departure;
            $ride->defaultRideDetail[0]->departure = $ride->defaultRideDetail[0]->destination;
            $ride->defaultRideDetail[0]->destination = $temp;

            // Swap pickup and dropoff locations
            $temp1 = $ride->pickup;
            $ride->pickup = $ride->dropoff;
            $ride->dropoff = $temp1;

            // Clear the date (user must select a new date for return ride)
            $ride->date = null;

            // Keep the time the same (already preserved from $ride->time)
        }

        if ($rides->isNotEmpty()) {
            // Fetch ratings where the driver_id matches the authenticated user's ID
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

            if ($ratings->count() > 0) {
                // Calculate total average
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $postRidePage = $this->getPostRidePageWithSettingDetail();

        $isNewForm = false;

        $totalNoOfRides = Ride::where('added_by', $user_id)
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
        $noShowsCount = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $cancellationCount = CancellationHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();

        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $vehicleTypes = $this->getVehicleTypesByLanguage();

        return view('post_ride', ['postRideSubDetailPage' => $postRideSubDetailPage, 'postRidePage' => $postRidePage, 
        'isNewForm' => $isNewForm, 'ride' => $ride, 'user' => $user, 'vehicles' => $vehicles, 
        'pinkRideSetting' => $pinkRideSetting, 
        'vehicleTypes' => $vehicleTypes, 
        'vehiclePage' => $vehiclePage, 
        'setting' => $setting, 'overallRating' => $overallRating, 
        'routeType' => 'repost', 'noshows' => $noshows, 'totalNoOfRides' => $totalNoOfRides, 'noShowsCount' => $noShowsCount, 'cancellationCount' => $cancellationCount]);
    }

    public function PostRide($lang = null)
    {
        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $pinkRideSetting = PinkRideSetting::first();
        $setting = FolkRideSetting::first();
        $vehicles = Vehicle::where('user_id', $user_id)->get();
        $rides = Ride::where('added_by', $user_id)->get();
        $noshows = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();

        $phone_verified = PhoneNumber::where('user_id', $user_id)->where('verified', '1')->first();
        if (!$phone_verified) {
            // phone number not verified, redirect to phone verification page
            return redirect()->route('phone', ['lang' => $lang]);
        }
        // Require driver's license on file (uploaded). Allow access once uploaded; admin approval (driver === '1') is not required to view/post ride form.
        $driver_license_on_file = User::where('id', $user_id)->whereNotNull('driver_license_upload')->where('driver_license_upload', '!=', '')->first();
        if (!$driver_license_on_file) {
            // driver license not on file, redirect to driver license verification page
            return redirect()->route('driver.verify', ['lang' => $lang]);
        }

        if ($rides->isNotEmpty()) {
            // Fetch ratings where the driver_id matches the authenticated user's ID
            $ratings = Rating::where(function ($query) use ($user_id) {
                // Ratings where type is 1 and ride_id belongs to the user
                $query->where('type', '1')
                    ->whereHas('ride', function ($query) use ($user_id) {
                        $query->where('added_by', $user_id);
                    });
            })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

            if ($ratings->count() > 0) {
                // Calculate total average
                $overallRating = $ratings->avg('average_rating') ?? 0;
            } else {
                $overallRating = 5;
            }
        } else {
            $overallRating = 5;
        }

        $postRidePage = $this->getPostRidePageWithSettingDetail();
        $postRideSubDetailPage = PostRidePageSettingSubDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return redirect()->route('home', ['lang' => $this->selectedLanguage->abbreviation])->with(['message' => "Your account has been suspended by the admin"]);
        }

        $ride = new Ride();
        $isNewForm = true;

        $totalNoOfRides = Ride::where('added_by', $user_id)
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
        $noShowsCount = NoShowHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
        $cancellationCount = CancellationHistory::where('user_id', $user_id)->where('type', 'driver')->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();

        $vehiclePage = MyVehicleSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $vehicleTypes = $this->getVehicleTypesByLanguage();

        return view('post_ride', [
            'totalRides' => $totalNoOfRides,
            'noShowsCount' => $noShowsCount,
            'cancellationCount' => $cancellationCount,
            'postRidePage' => $postRidePage,
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'isNewForm' => $isNewForm,
            'noshows' => $noshows,
            'ride' => $ride,
            'user' => $user,
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'vehiclePage' => $vehiclePage,
            'pinkRideSetting' => $pinkRideSetting,
            'setting' => $setting,
            'overallRating' => $overallRating,
            'routeType' => 'create'
        ]);
    }

    public function PostRideStore(Request $request)
    {

        $user_id = auth()->user()->id;
        $user = User::whereId($user_id)->first();
        $rides = Ride::where('added_by', $user_id)->get();
        $adminSetting = SiteSetting::first();

        $message = null;
        $selectedLanguage = session('selectedLanguage');
        if ($selectedLanguage) {
            // Find the language by abbreviation
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'overlap_ride_title', 'block_post_ride_message', 'not_allowed_post_ride_state_wise_message', 'profile_photo_required_message', 'overlap_ride_message', 'ride_dead_time_text')->first();
                $cityErrorMessage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first();
                $defaultLang = Language::where('is_default', 1)->first();
                $siteTextErrorMessage = SiteTextDetail::getByLanguageKeyedBySlug($selectedLanguage->id, $defaultLang ? $defaultLang->id : 1);
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $cityErrorMessage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first();
                $siteTextErrorMessage = SiteTextDetail::getByLanguageKeyedBySlug($selectedLanguage->id, $selectedLanguage->id);
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_post_message', 'ride_schedule_message', 'overlap_ride_title', 'block_post_ride_message', 'not_allowed_post_ride_state_wise_message', 'overlap_ride_message', 'profile_photo_required_message', 'ride_dead_time_text')->first();
            }
        }
        $siteTextErrorMessage = $siteTextErrorMessage ?? [];

        // Ensure stop_date and stop_time are arrays (form may send single value)
        if ($request->has('stop_date') && !is_array($request->stop_date)) {
            $request->merge(['stop_date' => [$request->stop_date]]);
        }
        if ($request->has('stop_time') && !is_array($request->stop_time)) {
            $request->merge(['stop_time' => [$request->stop_time]]);
        }

        // Normalize stop_date[] to Y-m-d; preserve keys so segment N (From = stop N-1) uses stop_date[N-1]
        if ($request->has('stop_date') && is_array($request->stop_date)) {
            $normalizedStopDates = [];
            foreach ($request->stop_date as $idx => $v) {
                if ($v === null || trim((string) $v) === '') {
                    $normalizedStopDates[$idx] = '';
                    continue;
                }
                try {
                    $normalizedStopDates[$idx] = Carbon::parse($v)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $normalizedStopDates[$idx] = '';
                }
            }
            $request->merge(['stop_date' => $normalizedStopDates]);
        }

        // Normalize stop_time[] to H:i:s; preserve keys so segment N (From = stop N-1) uses stop_time[N-1]
        if ($request->has('stop_time') && is_array($request->stop_time)) {
            $normalizedStopTimes = [];
            foreach ($request->stop_time as $idx => $v) {
                if ($v === null || trim((string) $v) === '') {
                    $normalizedStopTimes[$idx] = '';
                    continue;
                }
                try {
                    $normalizedStopTimes[$idx] = Carbon::parse($v)->format('H:i:s');
                } catch (\Throwable $e) {
                    $normalizedStopTimes[$idx] = '';
                }
            }
            $request->merge(['stop_time' => $normalizedStopTimes]);
        }

        if ($user->block_post_ride == '1') {
            return back()->with('message', $message->block_post_ride_message);
        }

        if (!isset($user->profile_image) || $user->profile_image == '' || in_array(basename($user->profile_image), ['male.png', 'female.png', 'neutral.png'])) {
            return back()->with('message', $message->profile_photo_required_message ?? 'For posting a ride profile photo is required');
        }

        // Check if user has suspanded
        if ($user->suspand === '1') {
            return back()->with('message', $this->successMessage['admin_block_account_message'] ?? 'Your account has been suspended by the admin');
        }

        // Feature gatekeeping logic for Pink Ride and Extra Care Ride
        if ($request->has('features') && is_array($request->features)) {
            $pinkRideSetting = \App\Models\PinkRideSetting::first();
            $folkRideSetting = \App\Models\FolkRideSetting::first();
            $postRidePage = \App\Models\PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();

            // Get feature IDs for Pink Ride and Extra Care Ride
            $pinkRideFeatureId = $postRidePage->features_option1->features_setting_id ?? null;
            $extraCareFeatureId = $postRidePage->features_option2->features_setting_id ?? null;

            // Check if Pink Ride feature is selected
            if ($pinkRideFeatureId && in_array($pinkRideFeatureId, $request->features)) {
                // GENDER VALIDATION: Only female users can post Pink Rides
                if ($pinkRideSetting && $pinkRideSetting->female === '1') {
                    // Check if user has admin override (pink_ride = '1')
                    if ($user->pink_ride !== '1') {
                        // If user is explicitly disabled (pink_ride = '0'), block them
                        if ($user->pink_ride === '0') {
                            return back()->with('message', 'You are not allowed to post Pink Rides. Please contact support if you believe this is an error.');
                        }
                        // If pink_ride is empty/null, check gender restriction
                        if ($user->gender !== 'female') {
                            return back()->with('message', 'Only female drivers can post Pink Rides.');
                        }
                    }
                }

                // Check if driver's license is required and uploaded
                if ($pinkRideSetting && $pinkRideSetting->driver_license === '1') {
                    if (empty($user->driver_license_upload)) {
                        return back()->with('message', 'A government-issued photo ID (driver\'s license) is required to post Pink Rides. Please upload your driver\'s license in your profile.');
                    }
                }
            }

            // Check if Extra Care Ride feature is selected
            if ($extraCareFeatureId && is_array($request->features) && in_array($extraCareFeatureId, $request->features)) {
                $extraCareError = $this->validateExtraCareEligibility($user);
                if ($extraCareError) {
                    return back()->with('message', $extraCareError);
                }
            }
        }

        $customMessages = [
            'date' => 'Invalid date format',
            // 'time' => 'Invalid time format',
            'array' => 'The :attribute must be an array',
            'max' => 'The :attribute may not be greater than :max characters',
            'max_words' => 'The :attribute may not be greater than 300 words',
            'file' => 'Please select a valid file',
            'mimes' => 'The :attribute must be a file of type: jpeg, png',
            'image.max' => 'Can not upload image size greater than 10MB',
            'uploaded' => 'The image is not uploaded yet',
            'numeric' => 'This field must be a number',
            'agree_terms.accepted' => $postRidePage->agree_term_error ?? 'You must agree to the terms to continue.',
        ];

        $skip_vehicle = $request->filled('skip_vehicle') ? $request->skip_vehicle : 0;
        $add_vehicle = $request->filled('add_vehicle') ? $request->add_vehicle : 0;
        $added_vehicle = $request->filled('added_vehicle') ? $request->added_vehicle : 0;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $destination_path = public_path('car_images');
            $file->move($destination_path, $filename);
        } elseif ($request->has('existing_image')) {
            $filename = $request->input('existing_image');
        } elseif ($skip_vehicle !== 0) {
            $filename = '';
        }

        $recurring = $request->filled('recurring') ? $request->recurring : 0;

        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
            'pickup' => 'required',
            'dropoff' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'details' => 'required|string|max_words:300',
            'seats' => 'required',
            'smoke' => 'required',
            'animal_friendly' => 'required',
            'features' => 'array',
            'booking_method' => 'required',
            'booking_type' => 'required',
            'luggage' => 'required',
            'price' => 'required|numeric|gt:0',
            'payment_method' => 'required',
            'notes' => 'nullable|string|max:300',
            'middle_seats' => 'required',
            'back_seats' => 'required',
            'agree_terms' => 'accepted',
            'image' => $request->has('existing_image') || $add_vehicle !== 0 ? 'nullable|mimes:jpeg,png,jpg,gif|max:10240' : 'required|mimes:jpeg,png,jpg,gif|max:10240',
            'make' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'model' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'vehicle_type' => $add_vehicle !== 0 ? 'required|integer|exists:features_setting_detail,features_setting_id' : 'nullable',
            'year' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'color' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'license_no' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'car_type' => $add_vehicle !== 0 ? 'required' : 'nullable',
            'vehicle_id' => $added_vehicle !== 0 ? 'required' : 'nullable',
            'recurring_type' => $recurring !== 0 ? 'required' : 'nullable',
            'recurring_trips' => $recurring !== 0 ? 'required|numeric|max:10' : 'nullable',
        ], [], $customMessages);
        // $cityErrorMessage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first();

        $fromSpot = $request->input('from');
        $from = explode(',', $fromSpot)[0];

        $toSpot = $request->input('to');
        $to = explode(',', $toSpot)[0];
        $from = trim($from);
        $to = trim($to);
        $cityValidator = Validator::make([
            'from' => $from,
            'to' => $to,
        ], [
            'from' => 'required|exists:cities,name',
            'to' => 'required|exists:cities,name',
        ], [
            'from.exists' => $cityErrorMessage->city_not_in_record,
            'to.exists' => $cityErrorMessage->city_not_in_record,
        ]);

        $nowDate = date('Y-m-d');
        $getRideCount = RideDetail::whereRaw('LOWER(`departure`) LIKE ? ', ['%' . $request->from . '%'])->where('date', $nowDate)->where('default_ride', '1')->whereHas('ride', function ($q) use ($nowDate, $user_id) {
            $q->where('date', $nowDate)->where('added_by', $user_id);
        })->count();

        $getRideCount = isset($getRideCount) ? $getRideCount : 0;

        $locationBeforeComma = explode(',', $request->from);

        $getFromState = City::with('state:id,abrv,ride_limit')->where('status', '1')->whereRaw('LOWER(`name`) LIKE ? ', ['%' . $locationBeforeComma[0] . '%'])->first();


        if (isset($getFromState) && !empty($getFromState)) {
            if (isset($getFromState->state->ride_limit) && $getRideCount >= $getFromState->state->ride_limit) {
                return back()->with('message', $message->not_allowed_post_ride_state_wise_message)->withInput();
            }
        }

        // Add custom logic to check at least one checkbox is selected
        if (!$request->has('skip_vehicle') && !$request->has('add_vehicle') && !$request->has('added_vehicle')) {
            $validator->after(function ($validator) {
                $validator->errors()->add('vehicle_selection', 'You must select at least one vehicle option.');
            });
        }

        if ($validator->fails()) {
            // Check if there are validation errors for the 'uploaded' attribute
            $hasRequiredError = $validator->errors()->has('image') && $validator->errors()->first('image') === 'The image is not uploaded yet';
            // If there are other validation errors or the 'image' error is not present, return back with errors and the uploaded image path
            if (!$hasRequiredError || $validator->errors()->count() > 1) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('uploaded_image', $filename ?? null);
            }
        }

        $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
        $formattedTime = strlen($request->time) <= 5
            ? Carbon::createFromFormat('H:i', $request->time)->format('H:i:s')
            : Carbon::parse($request->time)->format('H:i:s');

        // Check if any existing ride has the same date and time
        foreach ($rides as $existingRide) {
            if (
                $existingRide->date == $formattedDate &&
                $existingRide->time == $formattedTime
            ) {
                $oldInput = $request->all();
                return back()->with('error', $message->ride_schedule_message)->with('heading', $message->overlap_ride_title ?? 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
            }
        }

        //Second - Get distance and duration from Google Maps API
        $duration = 0;
        $distance = 0;
        // Use original from/to values - getDataFromGoogleApi will properly encode them
        $from = $request->from;
        $to = $request->to;
        $googleApiData = $this->getDataFromGoogleApi($from, $to);
        if (isset($googleApiData) && !empty($googleApiData)) {
            // Check element status first before accessing distance/duration
            $elementStatus = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['status']) ? $googleApiData['rows'][0]['elements'][0]['status'] : null;

            if ($elementStatus === 'OK') {
                $duration = isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;
                $distance = isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
            } else {
                // Log element status when it's not 'OK' for debugging
                Log::warning('Google Maps API element status is not OK for post ride', [
                    'from' => $from,
                    'to' => $to,
                    'element_status' => $elementStatus,
                    'api_status' => $googleApiData['status'] ?? 'unknown',
                    'error_message' => $googleApiData['error_message'] ?? 'No error message',
                    'full_response' => $googleApiData
                ]);
                $duration = 0;
                $distance = 0;
            }
        }

        if ($distance != 0) {
            $distance = round(($distance / 1000), 2);
        }

        Log::info('Distance calculation completed for post ride', [
            'from' => $from,
            'to' => $to,
            'distance_km' => $distance,
            'duration_seconds' => $duration,
            'distance_meters' => $distance * 1000,
            'element_status' => $elementStatus ?? 'unknown'
        ]);

        // Cost-sharing cap validation: Price per seat validation
        // Formula: (Distance × Cap) ÷ Seats = Max price per seat
        // Skip validation if user explicitly chose to bypass (after seeing warning)
        $bypassValidation = $request->has('bypass_price_validation') && $request->bypass_price_validation == '1';

        $priceWarningData = null; // Initialize variable
        if (!$bypassValidation && $distance > 0 && isset($request->price) && $request->price > 0 && isset($request->seats) && $request->seats > 0) {
            $seats = (int)$request->seats;
            $pricePerSeat = (float)$request->price;

            // Calculate max allowed price per seat using Error-Triggering Cap: $0.72/km
            $maxPricePerSeat = ($distance * 0.72) / $seats;

            // Calculate soft warning price per seat: $0.66/km
            $softWarningPricePerSeat = ($distance * 0.66) / $seats;

            Log::info('Price per seat calculation (PostRideStore)', [
                'price_per_seat' => $pricePerSeat,
                'distance_km' => $distance,
                'seats' => $seats,
                'max_price_per_seat' => round($maxPricePerSeat, 2),
                'soft_warning_price_per_seat' => round($softWarningPricePerSeat, 2),
                'error_cap' => 0.72,
                'warning_cap' => 0.66
            ]);

            // Error-Triggering Cap: $0.72 per km - BLOCK if exceeded
            if ($pricePerSeat > $maxPricePerSeat) {
                Log::warning('Price per seat exceeds error-triggering cap (PostRideStore)', [
                    'price_per_seat' => $pricePerSeat,
                    'max_allowed' => round($maxPricePerSeat, 2),
                    'cap' => 0.72
                ]);

                return back()->with('error', 'The price per seat ($' . number_format($pricePerSeat, 2) . ') exceeds the maximum allowed for cost-sharing rides ($' . number_format($maxPricePerSeat, 2) . ' per seat). Please adjust your price.')
                    ->with('heading', 'Price Limit Exceeded')
                    ->with('max_price_per_seat', round($maxPricePerSeat, 2))
                    ->withInput();
            }

            // Soft Warning Cap: $0.66 per km - WARN but ALLOW
            if ($pricePerSeat > $softWarningPricePerSeat) {
                Log::info('Price per seat exceeds soft warning cap but within error cap (PostRideStore)', [
                    'price_per_seat' => $pricePerSeat,
                    'soft_warning_price' => round($softWarningPricePerSeat, 2),
                    'warning_cap' => 0.66
                ]);

                // Return back to form with warning - user will see modal and can choose to proceed or adjust
                return back()->with('price_warning', [
                    'message' => 'The price you entered is above the standard reimbursement rate recommended by the CRA and Revenu Québec.',
                    'price_per_seat' => $pricePerSeat,
                    'soft_warning_price' => round($softWarningPricePerSeat, 2)
                ])->withInput();
            }
        }

        if (isset($adminSetting)) {

            if (isset($request->date) && isset($request->time)) {
                $rideDateTime = Carbon::parse("$request->date $request->time");
                $apiTime = 0;
                if ($duration != 0) {
                    $apiTime = round(($duration / 3600), 2);
                }

                // $rideDateTime->addHours($adminSetting->destination_hours);
                // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                $totalHours = $duration / 3600;
                $fullHours = floor($totalHours);
                $minutes = round(($totalHours - $fullHours) * 60);
                $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                    ->addMinutes($minutes);
                $destinationReachedDate = $rideDateTime->toDateString();
                $destinationReachedTime = $rideDateTime->toTimeString();
            }
        }

        $newStart = Carbon::parse("$request->date $request->time");
        $newEnd = Carbon::parse("$destinationReachedDate $destinationReachedTime");

        $rides = DB::table('rides')
            ->where('status', '!=', 2)
            ->where('added_by', $user_id)
            ->whereRaw("CONCAT(date, ' ', time) < ?", [$newEnd])
            ->whereRaw("CONCAT(destination_reached_date, ' ', destination_reached_time) > ?", [$newStart])
            ->first();

        if (isset($rides) && !empty($rides)) {
            $oldInput = $request->all();
            return back()->with('error', $message->overlap_ride_message ?? 'this ride overlaps with an existing ride you already have')->with('heading', $message->overlap_ride_title ?? 'Ride already schedule')->withInput($oldInput)->with('uploaded_image', $filename ?? null);
        }
        $rideDateTime = Carbon::parse($formattedDate . ' ' . $formattedTime);

        if ($rideDateTime->lte(Carbon::now()->addMinutes($adminSetting->ride_post_dead_time ?? 0))) {
            return redirect()->back()
                ->with('error', $message->ride_dead_time_text ?? 'The ride time you selected is too close. Please select a time that is more than 15 minutes in the future')
                ->withInput();
        }
        $max_back_seats = $request->filled('max_back_seats') ? $request->max_back_seats : 0;
        $accept_more_luggage = $request->filled('accept_more_luggage') ? $request->accept_more_luggage : 0;
        $open_customized = $request->filled('open_customized') ? $request->open_customized : 0;

        // Join the selected checkboxes with semicolons.
        $features = implode('=', $request->input('features', []));

        if ($skip_vehicle !== 0) {
            $make = '';
            $model = '';
            $vehicle_type = '';
            $year = '';
            $color = '';
            $license_no = '';
            $car_type = '';
        }

        if ($add_vehicle !== 0) {
            $make = $request->make;
            $model = $request->model;
            $vehicle_type = Ride::normalizeRideVehicleTypeId($request->vehicle_type);
            $year = $request->year;
            $color = $request->color;
            $license_no = $request->license_no;
            $car_type = $request->car_type;

            $vehicle = Vehicle::create([
                'user_id' => auth()->user()->id,
                'make' => $request->make,
                'model' => $request->model,
                'type' => Vehicle::normalizeVehicleTypeId($request->vehicle_type),
                'liscense_no' => $request->license_no,
                'color' => $request->color,
                'year' => $request->year,
                'car_type' => $request->car_type,
                'image' => $filename,
            ]);
            $vehicle_id = $vehicle->id;
        }

        if ($added_vehicle !== 0) {
            $vehicle = Vehicle::whereId($request->vehicle_id)->first();
            if ($vehicle) {
                $make = $vehicle->make;
                $model = $vehicle->model;
                $vehicle_type = $vehicle->type;
                $year = $vehicle->year;
                $color = $vehicle->color;
                $license_no = $vehicle->liscense_no;
                $car_type = $vehicle->car_type;
                $vehicle_id = $vehicle->id;
                if ($vehicle->remove_image === '0') {
                    $imageName = basename($vehicle->image);
                    $filename = $imageName;
                } else {
                    $filename = '';
                }
            } else {
                $make = '';
                $model = '';
                $vehicle_type = '';
                $year = '';
                $color = '';
                $license_no = '';
                $car_type = '';
            }
        }

        if ($recurring == 0) {
            $recurring_type = '';
            $recurring_trips = '';
        } else {
            $recurring_type = $request->recurring_type;
            $recurring_trips = $request->recurring_trips;
        }


        $initialRide = Ride::create([
            'departure' => "",
            'departure_lat' => '',
            'departure_lng' => '',
            'departure_place' => '',
            'departure_route' => '',
            'departure_zipcode' => '',
            'departure_city' => '',
            'departure_state' => '',
            'departure_state_short' => '',
            'departure_country' => '',

            'destination' => "",
            'destination_lat' => '',
            'destination_lng' => '',
            'destination_place' => '',
            'destination_route' => '',
            'destination_zipcode' => '',
            'destination_city' => '',
            'destination_state' => '',
            'destination_state_short' => '',
            'destination_country' => '',

            'total_distance' => "",
            'total_time' => "",
            'date' => $formattedDate,
            'time' => $formattedTime,

            'recurring' => $recurring,
            'recurring_type' => $recurring_type,
            'recurring_trips' => $recurring_trips,
            'details' => $request->details,
            'seats' => $request->seats,

            'skip_vehicle' => $skip_vehicle,
            'add_vehicle' => $add_vehicle,
            'added_vehicle' => $added_vehicle,
            'vehicle_id' => $vehicle_id ?? null,
            'make' => $make,
            'model' => $model,
            'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
            'year' => $year,
            'color' => $color,
            'license_no' => $license_no,
            'car_type' => $car_type,
            'car_image' => $filename,
            'car_image_original' => $filename,
            'smoke' => $request->smoke,
            'animal_friendly' => $request->animal_friendly,
            'features' => $features,
            'booking_method' => $request->booking_method,
            'booking_type' => $request->booking_type,
            'max_back_seats' => $max_back_seats,
            'luggage' => $request->luggage,
            'accept_more_luggage' => $accept_more_luggage,
            'open_customized' => $open_customized,
            'price' => "",
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'added_by' => $user_id,
            'until_date' => null,
            'until_limit' => '',

            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,

            'middle_seats' => $request->middle_seats,
            'back_seats' => $request->back_seats,
            'added_on' => now(),
        ]);

        //Add Seat Detail
        for ($i = 1; $i <= $initialRide->seats; $i++) {
            $seatDetail = new SeatDetail;
            $seatDetail->ride_id = $initialRide->id;
            $seatDetail->seat_number = $i;
            $seatDetail->status = 'pending';
            $seatDetail->save();
        }

        //Add Ride Detail

        $rideDetail = new RideDetail();
        $rideDetail->ride_id = $initialRide->id;
        $rideDetail->departure = $request->from;
        $rideDetail->destination = $request->to;
        $rideDetail->pickup = $request->pickup ?? null;
        $rideDetail->dropoff = $request->dropoff ?? null;
        $rideDetail->default_ride = 1;
        $rideDetail->total_distance = $distance;
        $rideDetail->total_duration = $duration;
        $rideDetail->price = $request->price;
        $rideDetail->time = $formattedTime;
        $rideDetail->date = $formattedDate;

        if (isset($adminSetting)) {

            if (isset($initialRide->date) && isset($initialRide->time)) {
                $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");
                $apiTime = 0;
                if ($duration != 0) {
                    $apiTime = round(($duration / 3600), 2);
                }

                // $rideDateTime->addHours($adminSetting->destination_hours);
                // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                $totalHours = $duration / 3600;
                $fullHours = floor($totalHours);
                $minutes = round(($totalHours - $fullHours) * 60);
                $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                    ->addMinutes($minutes);
                $destinationReachedDate = $rideDateTime->toDateString();
                $destinationReachedTime = $rideDateTime->toTimeString();


                $rideDateTime->addHours($adminSetting->ride_completed_hours);
                $completedDate = $rideDateTime->toDateString();
                $completedTime = $rideDateTime->toTimeString();

                $initialRide->completed_date = $completedDate ?? '';
                $initialRide->completed_time = $completedTime;
                $initialRide->destination_reached_date = $destinationReachedDate;
                $initialRide->destination_reached_time = $destinationReachedTime;
                $initialRide->save();

                $rideDetail->destination_time = $destinationReachedTime;
                $rideDetail->destination_date = $destinationReachedDate;
                $rideDetail->completed_time = $completedTime;
                $rideDetail->completed_date = $completedDate;
            }
        }
        $rideDetail->save();

        // If JS did not build from_spot[] / to_spot[] but stop_spot_display[] exists, build them server-side
        if ((!$request->has('from_spot') || empty($request->from_spot))
            && $request->has('stop_spot_display')
            && is_array($request->stop_spot_display)
        ) {
            $stops = array_values(array_filter($request->stop_spot_display, function ($v) {
                return trim((string) $v) !== '';
            }));
            if (!empty($stops) && $request->filled('from') && $request->filled('to')) {
                $origin = $request->from;
                $destination = $request->to;
                $fromSpot = [];
                $toSpot = [];
                $priceSpot = [];
                $mainPrice = $request->price;
                $segmentPrices = (array) $request->input('price_spot_display', []);
                $n = count($stops);
                for ($i = 0; $i <= $n; $i++) {
                    $fromVal = ($i === 0) ? $origin : ($stops[$i - 1] ?? null);
                    $toVal = ($i === $n) ? $destination : ($stops[$i] ?? null);
                    if (!$fromVal || !$toVal) {
                        continue;
                    }
                    $segPrice = $mainPrice;
                    if (isset($segmentPrices[$i]) && $segmentPrices[$i] !== '') {
                        $segPrice = $segmentPrices[$i];
                    }
                    $fromSpot[] = $fromVal;
                    $toSpot[] = $toVal;
                    $priceSpot[] = $segPrice;
                }
                if (!empty($fromSpot)) {
                    $request->merge([
                        'from_spot' => $fromSpot,
                        'to_spot' => $toSpot,
                        'price_spot' => $priceSpot,
                    ]);
                }
            }
        }

        if (isset($request->from_spot) && !empty($request->from_spot)) {
            $segmentDistances = [];
            $segmentDurations = [];
            $pointsForPairs = [$request->from];
            $validSegmentPrices = [];
            $validSegmentDistances = [];
            $validSegmentDurations = [];
            foreach ($request->from_spot as $key => $from_spot) {
                if (
                    empty($request->from_spot[$key]) ||
                    empty($request->to_spot[$key])
                ) {
                    continue;
                }

                $duration = 0;
                $distance = 0;

                $fromArray = explode(',', $request->from_spot[$key]);
                $toArray = explode(',', $request->to_spot[$key]);
                $googleApiData = $this->getDataFromGoogleApi($request->from_spot[$key], $request->to_spot[$key]);
                if (isset($googleApiData) && !empty($googleApiData)) {
                    // Check element status first before accessing distance/duration
                    $elementStatus = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['status']) ? $googleApiData['rows'][0]['elements'][0]['status'] : null;

                    if ($elementStatus === 'OK') {
                        $duration = isset($googleApiData['rows'][0]['elements'][0]['duration']) ? $googleApiData['rows'][0]['elements'][0]['duration']['value'] : 0;
                        $distance = isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
                    } else {
                        // Log element status when it's not 'OK' for debugging
                        Log::warning('Google Maps API element status is not OK for post ride spot', [
                            'spot_index' => $key,
                            'from' => $request->from_spot[$key],
                            'to' => $request->to_spot[$key],
                            'element_status' => $elementStatus,
                            'api_status' => $googleApiData['status'] ?? 'unknown',
                            'error_message' => $googleApiData['error_message'] ?? 'No error message',
                            'full_response' => $googleApiData
                        ]);
                        $duration = 0;
                        $distance = 0;
                    }
                }

                if ($distance != 0) {
                    $distance = round(($distance / 1000), 2);
                }

                $segmentDistances[$key] = $distance;
                $segmentDurations[$key] = $duration;
                $pointsForPairs[] = $request->to_spot[$key];
                $validSegmentPrices[] = $request->price_spot[$key] ?? 0;
                $validSegmentDistances[] = $distance;
                $validSegmentDurations[] = $duration;

                $rideDetail = new RideDetail();
                $rideDetail->ride_id = $initialRide->id;
                $rideDetail->departure = $request->from_spot[$key];
                $rideDetail->destination = $request->to_spot[$key];
                $pickupNote = $key === 0
                    ? ($request->pickup ?? null)
                    : (isset($request->stop_pickup_dropoff[$key - 1]) ? trim((string) $request->stop_pickup_dropoff[$key - 1]) : null);
                $dropoffNote = ($key === count($request->from_spot) - 1)
                    ? ($request->dropoff ?? null)
                    : (isset($request->stop_pickup_dropoff[$key]) ? trim((string) $request->stop_pickup_dropoff[$key]) : null);
                $rideDetail->pickup = ($pickupNote !== '' && $pickupNote !== null) ? $pickupNote : ($request->pickup ?? null);
                $rideDetail->dropoff = ($dropoffNote !== '' && $dropoffNote !== null) ? $dropoffNote : ($request->dropoff ?? null);
                $rideDetail->default_ride = 0;
                $rideDetail->total_distance = $distance;
                $rideDetail->total_duration = $duration;
                $rideDetail->price = $request->price_spot[$key];
                // Date/time when this segment's "From" (from_spot[$key]) is the departure: key 0 = main, key>0 = stop_date[key-1] / stop_time[key-1]
                $segmentDate = ($key > 0 && $request->has('stop_date') && is_array($request->stop_date) && isset($request->stop_date[$key - 1]) && (string) $request->stop_date[$key - 1] !== '')
                    ? $request->stop_date[$key - 1]
                    : $formattedDate;
                $segmentTime = ($key > 0 && $request->has('stop_time') && is_array($request->stop_time) && isset($request->stop_time[$key - 1]) && (string) $request->stop_time[$key - 1] !== '')
                    ? $request->stop_time[$key - 1]
                    : $formattedTime;
                if (strlen($segmentTime) <= 5 && $segmentTime !== '') {
                    try {
                        $segmentTime = Carbon::createFromFormat('H:i', $segmentTime)->format('H:i:s');
                    } catch (\Throwable $e) {
                    }
                }
                $rideDetail->date = $segmentDate;
                $rideDetail->time = $segmentTime;

                if (isset($adminSetting)) {

                    if (isset($initialRide->date) && isset($initialRide->time)) {
                        $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");

                        $apiTime = 0;
                        if ($duration != 0) {
                            $apiTime = round(($duration / 3600), 2);
                        }

                        // $rideDateTime->addHours($adminSetting->destination_hours);
                        // $rideDateTime->addMinutes(($apiTime - floor($apiTime)) * 60);
                        $totalHours = $duration / 3600;  // e.g., 109800 seconds → 30.5 hours
                        $fullHours = floor($totalHours);  // 30 hours
                        $minutes = round(($totalHours - $fullHours) * 60);  // 30 minutes
                        $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)
                            ->addMinutes($minutes);
                        $destinationReachedDate = $rideDateTime->toDateString();
                        $destinationReachedTime = $rideDateTime->toTimeString();


                        $rideDateTime->addHours($adminSetting->ride_completed_hours);
                        $completedDate = $rideDateTime->toDateString();
                        $completedTime = $rideDateTime->toTimeString();

                        $rideDetail->destination_time = $destinationReachedTime;
                        $rideDetail->destination_date = $destinationReachedDate;
                        $rideDetail->completed_time = $completedTime;
                        $rideDetail->completed_date = $completedDate;
                    }
                }
                $rideDetail->save();
            }

            // Create RideDetails for every (origin, destination) pair so the ride appears in all segment searches
            $numPoints = count($pointsForPairs);
            for ($i = 0; $i < $numPoints; $i++) {
                for ($j = $i + 2; $j < $numPoints; $j++) {
                    // Skip full route (origin to destination); it is already the default RideDetail
                    if ($i === 0 && $j === $numPoints - 1) {
                        continue;
                    }
                    $compositePrice = 0;
                    $compositeDistance = 0;
                    $compositeDuration = 0;
                    for ($k = $i; $k < $j; $k++) {
                        $compositePrice += $validSegmentPrices[$k] ?? 0;
                        $compositeDistance += $validSegmentDistances[$k] ?? 0;
                        $compositeDuration += $validSegmentDurations[$k] ?? 0;
                    }
                    $compositeDetail = new RideDetail();
                    $compositeDetail->ride_id = $initialRide->id;
                    $compositeDetail->departure = $pointsForPairs[$i];
                    $compositeDetail->destination = $pointsForPairs[$j];
                    $pickupNote = $i === 0
                        ? ($request->pickup ?? null)
                        : (isset($request->stop_pickup_dropoff[$i - 1]) ? trim((string) $request->stop_pickup_dropoff[$i - 1]) : null);
                    $dropoffNote = $j === $numPoints - 1
                        ? ($request->dropoff ?? null)
                        : (isset($request->stop_pickup_dropoff[$j - 1]) ? trim((string) $request->stop_pickup_dropoff[$j - 1]) : null);
                    $compositeDetail->pickup = ($pickupNote !== '' && $pickupNote !== null) ? $pickupNote : ($request->pickup ?? null);
                    $compositeDetail->dropoff = ($dropoffNote !== '' && $dropoffNote !== null) ? $dropoffNote : ($request->dropoff ?? null);
                    $compositeDetail->default_ride = 0;
                    $compositeDetail->total_distance = $compositeDistance;
                    $compositeDetail->total_duration = $compositeDuration;
                    $compositeDetail->price = $compositePrice;
                    $compositeSegmentTime = ($i > 0 && $request->has('stop_time') && is_array($request->stop_time) && isset($request->stop_time[$i - 1]) && (string) $request->stop_time[$i - 1] !== '')
                        ? $request->stop_time[$i - 1]
                        : $formattedTime;
                    if (strlen($compositeSegmentTime) <= 5 && $compositeSegmentTime !== '') {
                        try {
                            $compositeSegmentTime = Carbon::createFromFormat('H:i', $compositeSegmentTime)->format('H:i:s');
                        } catch (\Throwable $e) {
                        }
                    }
                    $compositeDetail->time = $compositeSegmentTime;
                    $compositeSegmentDate = ($i > 0 && $request->has('stop_date') && is_array($request->stop_date) && isset($request->stop_date[$i - 1]) && (string) $request->stop_date[$i - 1] !== '')
                        ? $request->stop_date[$i - 1]
                        : Carbon::parse($request->date)->format('Y-m-d');
                    $compositeDetail->date = $compositeSegmentDate;
                    if (isset($adminSetting) && isset($initialRide->date) && isset($initialRide->time)) {
                        $cumulativeDurationToJ = 0;
                        for ($k = 0; $k < $j; $k++) {
                            $cumulativeDurationToJ += $validSegmentDurations[$k] ?? 0;
                        }
                        $rideDateTime = Carbon::parse("$initialRide->date $initialRide->time");
                        $totalHours = $cumulativeDurationToJ / 3600;
                        $fullHours = floor($totalHours);
                        $minutes = round(($totalHours - $fullHours) * 60);
                        $rideDateTime->addHours($adminSetting->destination_hours + $fullHours)->addMinutes($minutes);
                        $compositeDetail->destination_time = $rideDateTime->toTimeString();
                        $compositeDetail->destination_date = $rideDateTime->toDateString();
                        $rideDateTime->addHours($adminSetting->ride_completed_hours);
                        $compositeDetail->completed_time = $rideDateTime->toTimeString();
                        $compositeDetail->completed_date = $rideDateTime->toDateString();
                    }
                    $compositeDetail->save();
                }
            }
        }


        $recurring_id = $initialRide->id;

        // Check if the ride is recurring
        if ($recurring !== 0) {
            // Determine the frequency and number of recurring trips
            $frequency = $request->input('recurring_type');
            $numRecurringTrips = $request->input('recurring_trips');

            // Calculate the date interval based on the frequency
            $dateInterval = ($frequency === 'Daily') ? 'P1D' : 'P7D';

            // Create additional rides based on the recurring settings
            for ($i = 1; $i <= $numRecurringTrips; $i++) {
                $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                $initialRide = Ride::create([
                    'departure' => "",
                    'departure_lat' => '',
                    'departure_lng' => '',
                    'departure_place' => '',
                    'departure_route' => '',
                    'departure_zipcode' => '',
                    'departure_city' => '',
                    'departure_state' => '',
                    'departure_state_short' => '',
                    'departure_country' => '',

                    'destination' => "",
                    'destination_lat' => '',
                    'destination_lng' => '',
                    'destination_place' => '',
                    'destination_route' => '',
                    'destination_zipcode' => '',
                    'destination_city' => '',
                    'destination_state' => '',
                    'destination_state_short' => '',
                    'destination_country' => '',

                    'total_distance' => "",
                    'total_time' => "",
                    'date' => $nextDate->format('Y-m-d'),
                    'time' => $request->time,
                    'completed_date' => $nextCompletedDate->format('Y-m-d'),
                    'completed_time' => $initialRide->completed_time,
                    'destination_reached_date' => $nextDestinationReachedDate->format('Y-m-d'),
                    'destination_reached_time' => $initialRide->destination_reached_time,

                    'recurring' => $recurring,
                    'recurring_type' => '',
                    'recurring_trips' => '',
                    'recurring_id' => $recurring_id,
                    'details' => $request->details,
                    'seats' => $request->seats,

                    'skip_vehicle' => $skip_vehicle,
                    'add_vehicle' => $add_vehicle,
                    'added_vehicle' => $added_vehicle,
                    'vehicle_id' => $vehicle_id ?? null,
                    'make' => $make,
                    'model' => $model,
                    'vehicle_type' => Ride::normalizeRideVehicleTypeId($vehicle_type),
                    'year' => $year,
                    'color' => $color,
                    'license_no' => $license_no,
                    'car_type' => $car_type,
                    'car_image' => $filename,
                    'car_image_original' => $filename,
                    'smoke' => $request->smoke,
                    'animal_friendly' => $request->animal_friendly,
                    'features' => $features,
                    'booking_method' => $request->booking_method,
                    'booking_type' => $request->booking_type,
                    'max_back_seats' => $max_back_seats,
                    'luggage' => $request->luggage,
                    'accept_more_luggage' => $accept_more_luggage,
                    'open_customized' => $open_customized,
                    'price' => "",
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                    'added_by' => $user_id,
                    'until_date' => null,
                    'until_limit' => '',

                    'pickup' => $request->pickup,
                    'dropoff' => $request->dropoff,

                    'middle_seats' => $request->middle_seats,
                    'back_seats' => $request->back_seats,
                    'added_on' => now(),
                ]);


                for ($j = 1; $j <= $initialRide->seats; $j++) {
                    $seatDetail = new SeatDetail;
                    $seatDetail->ride_id = $initialRide->id;
                    $seatDetail->seat_number = $j;
                    $seatDetail->status = 'pending';
                    $seatDetail->save();
                }

                $getRideDetails = RideDetail::where('ride_id', $recurring_id)->get();
                foreach ($getRideDetails as $key => $getRideDetail) {
                    $nextDate = Carbon::parse($initialRide->date)->add(new \DateInterval($dateInterval));
                    $nextCompletedDate = Carbon::parse($initialRide->completed_date)->add(new \DateInterval($dateInterval));
                    $nextDestinationReachedDate = Carbon::parse($initialRide->destination_reached_date)->add(new \DateInterval($dateInterval));

                    $rideDetail = new RideDetail();
                    $rideDetail->ride_id = $initialRide->id;
                    $rideDetail->departure = $getRideDetail->departure;
                    $rideDetail->destination = $getRideDetail->destination;
                    $rideDetail->pickup = $getRideDetail->pickup ?? null;
                    $rideDetail->dropoff = $getRideDetail->dropoff ?? null;
                    $rideDetail->default_ride = $getRideDetail->default_ride;
                    $rideDetail->total_distance = $getRideDetail->total_distance;
                    $rideDetail->total_duration = $getRideDetail->total_duration;
                    $rideDetail->price = $getRideDetail->price;
                    $rideDetail->time = $getRideDetail->time;
                    $rideDetail->date = $nextDate;
                    $rideDetail->destination_time = $initialRide->destination_time;
                    $rideDetail->destination_date = $nextDestinationReachedDate;
                    $rideDetail->completed_time = $initialRide->completed_time;
                    $rideDetail->completed_date = $nextCompletedDate;
                    $rideDetail->save();
                }
            }
        }
        if (isset($user->email_notification) && $user->email_notification == 1) {
            $features = explode('=', $initialRide->features);

            $data = [
                'username' => $user->first_name,
                'from' => $request->from,
                'to' => $request->to,
                'on' => $request->date,
                'at' => $request->time,
                'seats' => $request->seats,
                'price' => $request->price,
                'redirect' => env('APP_URL') . '/' . $selectedLanguage->abbreviation . '/my-rides',

            ];
            if (in_array('1', $features) && in_array('2', $features)) {
                // Both Pink and Extra+
                Mail::to($user->email)->queue(new PinkExtraCareRideMail($data));
            } elseif (in_array('1', $features)) {
                // Only Pink Ride
                Mail::to($user->email)->queue(new PinkRideMail($data));
            } elseif (in_array('2', $features)) {
                // Only Extra+ Ride
                Mail::to($user->email)->queue(new ExtraCareRideMail($data));
            } else {
                // Regular ride (existing email)
                Mail::to($user->email)->queue(new RidePostedMail($data));
            }
        }

        $features = explode('=', $initialRide->features);

        $hasVehicle = !empty($initialRide->vehicle_id);
        $liveMessage = getNotificationMessageText(
            $hasVehicle ? 'ride_live_standard' : 'ride_live_requires_vehicle',
            $user,
            [],
            $hasVehicle ? 'Your ride is now live on ProximaRide' : 'Add your vehicle to make your ride live'
        );
        $pinkLiveMessage = getNotificationMessageText(
            $hasVehicle ? 'ride_live_pink' : 'ride_live_requires_vehicle',
            $user,
            [],
            $hasVehicle ? 'Your Pink Ride is now live on ProximaRide' : 'Add your vehicle to make your ride live'
        );
        $extraCareLiveMessage = getNotificationMessageText(
            $hasVehicle ? 'ride_live_extra_care' : 'ride_live_requires_vehicle',
            $user,
            [],
            $hasVehicle ? 'Your Extra+ Ride is now live on ProximaRide' : 'Add your vehicle to make your ride live'
        );
        $pinkExtraCareLiveMessage = getNotificationMessageText(
            $hasVehicle ? 'ride_live_pink_extra_care' : 'ride_live_requires_vehicle',
            $user,
            [],
            $hasVehicle ? 'Your Pink and Extra+ ride is now live on ProximaRide' : 'Add your vehicle to make your ride live'
        );

        if (in_array('1', $features) && in_array('2', $features)) {
            // Both Pink and Extra+
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user->id,
                'message' =>  $pinkExtraCareLiveMessage,
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        } elseif (in_array('1', $features)) {
            // Only Pink Ride
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user->id,
                'message' =>  $pinkLiveMessage,
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        } elseif (in_array('2', $features)) {
            // Only Extra+ Ride
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user->id,
                'message' =>  $extraCareLiveMessage,
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        } else {
            // Regular ride (existing email)
            $notification = Notification::create([
                'ride_id' => $initialRide->id,
                'posted_by' => $user->id,
                'message' =>  $liveMessage,
                'status' => 'upcoming',
                'notification_type' => 'upcoming',
                'ride_detail_id' => $initialRide->rideDetail[0]->id,
                'departure' => $initialRide->rideDetail[0]->departure,
                'destination' => $initialRide->rideDetail[0]->destination
            ]);

            $body = $notification->message;
            $fcmService = new FCMService();

            $fcmToken = $user->mobile_fcm_token;
            if ($fcmToken) {
                $fcmService->sendNotification($fcmToken, $body);
            }

            $fcm_tokens = FCMToken::where('user_id', $user->id)->get();

            foreach ($fcm_tokens as $fcm_token) {
                try {
                    $fcmService->sendNotification($fcm_token->token, $body);
                } catch (\Exception $e) {
                    Log::error("FCM Notification failed for token: $fcm_token, Error: " . $e->getMessage());
                }
            }
        }

        // Prepare redirect data
        $redirectData = [
            'message' => $message->ride_post_message,
            'id' => $initialRide->id
        ];

        // Include price_warning in redirect if it exists (soft warning cap)
        if ($priceWarningData !== null) {
            $redirectData['price_warning'] = $priceWarningData;
        } elseif (session()->has('price_warning')) {
            $redirectData['price_warning'] = session('price_warning');
        }

        return redirect()->route('my_rides', ['lang' => $selectedLanguage->abbreviation])->with($redirectData)->withInput();
    }


    /*
     * AJAX only: returns HTML for a new "add more spots" row. Does NOT save to the database.
     * Extra spots are saved when the user submits the main form (UpdateRide or PostRideStore).
     */
    public function addNewSpots(Request $request)
    {
        $fromSpot = $request->input('from_spot');
        $toSpot = $request->input('to_spot');
        $from_city = $fromSpot ? trim(explode(',', $fromSpot)[0]) : '';
        $to_city = $toSpot ? trim(explode(',', $toSpot)[0]) : '';

        $selectedLanguage = session('selectedLanguage');
        $postRidePage = null;
        $postRideSubDetailPage = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $postRidePage = PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->first();
                $postRideSubDetailPage = PostRidePageSettingSubDetail::where('language_id', $selectedLanguage->id)->first();
            }
        }
        $cityErrorMessage = $selectedLanguage
            ? PostRidePageSettingDetail::where('language_id', $selectedLanguage->id)->select('city_not_in_record')->first()
            : null;

        $validator = Validator::make($request->all(), [
            'from_spot' => 'required|exists:cities,name',
            'to_spot' => 'required|exists:cities,name',
            'price' => 'required',

        ], [
            'from_spot.exists' => $cityErrorMessage->city_not_in_record ?? 'City not in record',
            'to_spot.exists' => $cityErrorMessage->city_not_in_record ?? 'City not in record',
        ]);

        if ((!$from_city || !DB::table('cities')->where('name', $from_city)->exists()) || (!$to_city || !DB::table('cities')->where('name', $to_city)->exists()) || is_null($request->price)) {
            // return response()->json([
            //     'status' => 'error',
            //     'errors' => $validator->errors(),
            // ]);
            if (is_null($request->price)) {

                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'price' => [__('validation.required', ['attribute' => 'price'])]
                    ],
                ]);
            }
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'from_spot' => [$cityErrorMessage->city_not_in_record ?? 'City not in record'],
                    'to_spot' =>  [$cityErrorMessage->city_not_in_record ?? 'City not in record'],
                ],
            ]);
        }

        $spotHtml = view('post_ride_partial.add_more_from_to_partial', [
            'postRideSubDetailPage' => $postRideSubDetailPage,
            'index' => $request->index,
            'postRidePage' => $postRidePage,
            'ride_detail' => null,
            'type' => 'create',
        ])->render();
        return response()->json(['spotHtml' => $spotHtml]);
    }

    public function deleteSpots(Request $request)
    {
        $selectedLanguage = session('selectedLanguage');
        $message = null;
        if ($selectedLanguage) {
            $selectedLanguage = Language::where('abbreviation', $selectedLanguage)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_has_booking_message')->first();
            }
        } else {
            $selectedLanguage = Language::where('is_default', 1)->first();
            if ($selectedLanguage) {
                $message = SuccessMessagesSettingDetail::where('language_id', $selectedLanguage->id)->select('ride_has_booking_message')->first();
            }
        }

        $checkBooking = Booking::where('ride_detail_id', $request->rideDetailId)->first();
        if (isset($checkBooking) && !empty($checkBooking)) {
            return response()->json(['status' => 'error', 'message' => $message->ride_has_booking_message ?? "ride has booking"]);
        }

        RideDetail::where('id', $request->rideDetailId)->delete();

        return response()->json(['status' => 'success']);
    }


    public function getDataFromGoogleApi($from, $to)
    {
        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        Log::info('Google Maps API Key: ' . $apiKey);
        // URL encode the addresses to properly handle spaces and special characters
        // This ensures city names like "Montreal, QC" and "Ottawa, ON" work correctly
        $fromEncoded = urlencode($from);
        $toEncoded = urlencode($to);

        $apiUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $fromEncoded . "&destinations=" . $toEncoded . "&units=imperial&key=" . $apiKey . "";

        Log::info('Google Maps API Request', [
            'from' => $from,
            'to' => $to,
            'from_encoded' => $fromEncoded,
            'to_encoded' => $toEncoded,
            'url' => str_replace($apiKey, 'HIDDEN_KEY', $apiUrl)
        ]);

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Google Maps API cURL Error: ' . curl_error($ch), [
                'from' => $from,
                'to' => $to,
                'curl_error' => curl_error($ch),
                'curl_errno' => curl_errno($ch)
            ]);
        }

        curl_close($ch);

        $data = json_decode($response, true);

        // Log API response
        if (isset($data['status']) && $data['status'] === 'OK') {
            $distance = isset($data['rows'][0]['elements'][0]['distance']['value']) ? $data['rows'][0]['elements'][0]['distance']['value'] : 0;
            $distanceText = isset($data['rows'][0]['elements'][0]['distance']['text']) ? $data['rows'][0]['elements'][0]['distance']['text'] : 'N/A';
            $duration = isset($data['rows'][0]['elements'][0]['duration']['value']) ? $data['rows'][0]['elements'][0]['duration']['value'] : 0;
            $durationText = isset($data['rows'][0]['elements'][0]['duration']['text']) ? $data['rows'][0]['elements'][0]['duration']['text'] : 'N/A';

            Log::info('Google Maps API Success', [
                'from' => $from,
                'to' => $to,
                'distance_meters' => $distance,
                'distance_km' => round($distance / 1000, 2),
                'distance_text' => $distanceText,
                'duration_seconds' => $duration,
                'duration_text' => $durationText,
                'status' => $data['status']
            ]);
        } else {
            // Log if API returns an error status
            Log::warning('Google Maps API returned non-OK status', [
                'status' => $data['status'] ?? 'unknown',
                'error_message' => $data['error_message'] ?? 'No error message',
                'from' => $from,
                'to' => $to,
                'response' => $data
            ]);
        }

        return $data;
    }

    /**
     * Validate that the user is eligible to post Extra Care Rides (rating, age, completed rides, no-shows, cancellations, verification).
     * Returns an error message string if ineligible, or null if eligible.
     */
    protected function validateExtraCareEligibility(User $user)
    {
        $setting = FolkRideSetting::first();
        if (!$setting) {
            return null;
        }

        if ($user->folks_ride === '0') {
            return 'You are not allowed to post Extra Care Rides. Please contact support if you believe this is an error.';
        }

        if ($user->folks_ride !== '1') {
            if ($setting->verfiy_phone === '1') {
                $hasVerifiedPhone = $user->relationLoaded('phone_numbers')
                    ? $user->phoneNumbers->contains('verified', 1)
                    : $user->phoneNumbers()->where('verified', 1)->exists();
                if (!$hasVerifiedPhone) {
                    return 'A verified phone number is required to post Extra Care Rides.';
                }
            }
            if ($setting->verify_email === '1' && $user->email_verified !== '1') {
                return 'A verified email is required to post Extra Care Rides.';
            }
            if ($setting->driver_license === '1') {
                if ($user->driver !== '1') {
                    return 'Driver verification is required to post Extra Care Rides.';
                }
                if (empty($user->driver_license_upload)) {
                    return 'A government-issued photo ID (driver\'s license) is required to post Extra Care Rides. Please upload your driver\'s license in your profile.';
                }
            }
            if (empty($user->government_issued_id ?? $user->government_id ?? null) || empty($user->address ?? '')) {
                return 'A complete address and government-issued ID are required to post Extra Care Rides.';
            }

            $noShowsCount = NoShowHistory::where('user_id', $user->id)->where('type', 'driver')
                ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->count();
            if ($noShowsCount > 0) {
                return 'Drivers with recent no-shows cannot post Extra Care Rides.';
            }
            $cancellationCount = CancellationHistory::where('user_id', $user->id)->where('type', 'driver')
                ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])->whereNotNull('booking_id')->count();
            if ($cancellationCount > 0) {
                return 'Drivers with recent cancellations cannot post Extra Care Rides.';
            }

            $ratings = Rating::where('type', 1)->where('status', 1)
                ->whereHas('ride', fn($q) => $q->where('added_by', $user->id))->get();
            $overallRating = $ratings->isEmpty() ? 0 : $ratings->avg('average_rating');
            $minRating = (float) ($setting->average_rating ?? 0);
            if ($overallRating < $minRating) {
                return 'Extra Care Rides require a minimum driver rating of ' . $minRating . ' stars. Your current rating is ' . number_format((float) $overallRating, 1) . '.';
            }

            $age = $user->dob ? Carbon::parse($user->dob)->diffInYears(Carbon::now()) : 0;
            $minAge = (int) ($setting->driver_age ?? 0);
            if ($minAge > 0 && $age < $minAge) {
                return 'Extra Care Rides require drivers to be at least ' . $minAge . ' years old.';
            }

            $rideLimit = (int) ($setting->extra_rides_trip_limit ?? 0);
            if ($rideLimit > 0) {
                $totalCompleted = Ride::where('added_by', $user->id)->where('status', '!=', 2)
                    ->where(function ($q) {
                        $q->where(function ($q) {
                            $q->whereDate('completed_date', '<', now()->toDateString())
                                ->orWhere(function ($q) {
                                    $q->whereDate('completed_date', '=', now()->toDateString())
                                        ->whereTime('completed_time', '<', now()->toTimeString());
                                });
                        });
                    })->count();
                if ($totalCompleted < $rideLimit) {
                    return 'Extra Care Rides require at least ' . $rideLimit . ' completed rides.';
                }
            }
        }

        return null;
    }
}
