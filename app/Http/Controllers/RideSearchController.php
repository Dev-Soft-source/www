<?php

namespace App\Http\Controllers;

use App\Http\Requests\Px\PxStoreRideRequest;
use App\Models\PxBooking;
use App\Models\PxOption;
use App\Models\PxOptionGroup;
use App\Models\PxRide;
use App\Models\Booking;
use App\Models\Ride;
use App\Models\Vehicle;
use App\Models\TripsPageSettingDetail;
use App\Models\RideDetailPageSettingDetail;
use App\Models\SuccessMessagesSettingDetail;
use App\Models\SiteSetting;
use App\Models\FolkRideSetting;
use App\Models\PostRidePageSettingDetail;
use App\Models\User;
use App\Models\NoShowHistory;
use App\Models\CancellationHistory;
use App\Models\Rating;
use App\Models\PhoneNumber;
use App\Models\City;
use App\Models\State;
use App\Models\SeatDetail;
use App\Models\PxRideStop;
use App\Models\RecentSearch;
use App\Models\HomePageSettingDetail;
use App\Models\ExtraCareFaqDetail;
use App\Models\FeaturesSetting;
use App\Models\FeaturesSettingDetail;
use App\Models\PinkRideFaqDetail;
use App\Models\ProfilePageSettingDetail;
use App\Models\ProfileSettingDetail;
use App\Models\MyReviewSettingDetail;
use App\Services\PxRideService;
use App\Services\FCMService;
use App\Models\Notification;
use App\Models\FCMToken;
use App\Mail\RidePostedMail;
use App\Mail\PinkRideMail;
use App\Mail\ExtraCareRideMail;
use App\Mail\PinkExtraCareRideMail;
use App\Models\FindRidePageSettingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class RideSearchController extends Controller
{
    /**
     * Cached success messages for validation
     *
     * @var \App\Models\SuccessMessagesSettingDetail|null
     */
    protected $successMessages = null;





    public function folk_ride_search(Request $request, $lang = null)
    {
        $extraCareFaqs = ExtraCareFaqDetail::where('language_id', $this->selectedLanguage->id)->get();
        view()->share('extraCareFaqs', $extraCareFaqs);

        return $this->search($request, $lang, 'search_folk_ride');
    }

    public function pink_ride_search(Request $request, $lang = null)
    {
        $pinkRideFaqs = PinkRideFaqDetail::where('language_id', $this->selectedLanguage->id)->get();
        view()->share('pinkRideFaqs', $pinkRideFaqs);

        return $this->search($request, $lang, 'search_pink_ride');
    }

    public function proximalocal_ride_search(Request $request, $lang = null)
    {
        $proximaLocalRideFaqs = [];
        view()->share('proximaLocalRideFaqs', $proximaLocalRideFaqs);

        return $this->search($request, $lang, 'search_proximalocal_ride');
    }

    /**
     * Validate search request and redirect to GET route with query params
     */
    public function validateAndSearch(Request $request, $lang = null)
    {
        return $this->validateAndRedirect($request, $lang, 'search_ride', 'search_ride');
    }

    public function validateAndFolkRideSearch(Request $request, $lang = null)
    {
        $extraCareFaqs = ExtraCareFaqDetail::where('language_id', $this->selectedLanguage->id)->get();
        view()->share('extraCareFaqs', $extraCareFaqs);

        return $this->validateAndRedirect($request, $lang, 'folk_ride', 'search_folk_ride');
    }

    public function validateAndPinkRideSearch(Request $request, $lang = null)
    {
        $pinkRideFaqs = PinkRideFaqDetail::where('language_id', $this->selectedLanguage->id)->get();
        view()->share('pinkRideFaqs', $pinkRideFaqs);

        return $this->validateAndRedirect($request, $lang, 'pink_ride', 'search_pink_ride');
    }

    public function validateAndProximaLocalRideSearch(Request $request, $lang = null)
    {
        $proximaLocalRideFaqs = [];
        view()->share('proximaLocalRideFaqs', $proximaLocalRideFaqs);

        return $this->validateAndRedirect($request, $lang, 'proximalocal_ride', 'search_proximalocal_ride');
    }

    /**
     * Validate search request and redirect to GET route with query params
     */
    protected function validateAndRedirect(Request $request, $lang = null, $routeName, $view = 'search_ride')
    {
        $selectedLangId = optional($this->selectedLanguage)->id;
        $defaultLangId = optional($this->defaultLang)->id;
        $searchFilters = $this->getPxSearchFilters($request);
        $hasActiveFilters = collect($searchFilters)->contains(function ($value) {
            return $value !== null && $value !== '' && $value !== false;
        });

        $keyword = trim((string) $request->input('keyword'));
        $originLabel = trim((string) $request->input('origin.label'));
        $destinationLabel = trim((string) $request->input('destination.label'));
        $originCityId = $request->input('origin.city_id');
        $destinationCityId = $request->input('destination.city_id');
        $departureDate = $request->input('departure_date');

        // Validation rules
        $validator = Validator::make($request->all(), [
            'origin.label' => ['required_without:keyword'],
            'destination.label' => ['required_without:keyword'],
            'departure_date' => ['nullable', 'date'],
            'origin.city_id' => ['required_without:keyword'],
            'destination.city_id' => ['required_without:keyword'],
        ]);

        $invalidCityMessage = __('validation.custom.city_not_in_record.message')
            ?? 'Please select a valid city from the dropdown.';


        $validator->after(function ($validator) use ($request, $invalidCityMessage, $hasActiveFilters, $keyword, $originLabel, $destinationLabel, $originCityId, $destinationCityId) {
            $hasValidOrigin = $originLabel !== '' && !empty($originCityId);
            $hasValidDestination = $destinationLabel !== '' && !empty($destinationCityId);

            if ($keyword === '' && $originLabel === '' && $destinationLabel === '' && !$hasActiveFilters) {
                $validator->errors()->add('keyword', 'Enter a keyword, select at least one valid city from the dropdown, or choose a filter.');
            }

            if ($keyword === '' && ($originLabel !== '' || $destinationLabel !== '')) {
                if ($hasValidOrigin && $destinationLabel === '') {
                    $validator->errors()->add('destination.label', __('validation.custom.destination.label.required'));
                }

                if ($hasValidDestination && $originLabel === '') {
                    $validator->errors()->add('origin.label', __('validation.custom.origin.label.required'));
                }
            }

            if ($originLabel !== '' && empty($originCityId)) {
                $validator->errors()->add('origin.label', $invalidCityMessage);
            }

            if ($destinationLabel !== '' && empty($destinationCityId)) {
                $validator->errors()->add('destination.label', $invalidCityMessage);
            }
        });

        if ($validator->fails()) {
            return redirect()->route($routeName, ['lang' => $lang])
                ->withErrors($validator)
                ->withInput();
        }

        // Validation passed - redirect to GET route with query params
        $queryParams = $request->only([
            'origin',
            'destination',
            'departure_date',
            'keyword',
            'search',
            'women_only',
            'extra_care',
            'hide_full_rides',
            'driver_age',
            'driver_rating',
            'driver_phone',
            'driver_name',
            'ride_option_ids',
            'booking_method',
            'vehicle_type',
            'luggage_size',
            'smoking_allowed',
            'pets_allowed',
        ]);

        // Ensure search=1 is set when submitting
        if ($request->has('search')) {
            $queryParams['search'] = 1;
        }

        return redirect()->route($routeName, array_merge(['lang' => $lang], $queryParams));
    }

    public function search(Request $request, $lang = null, $view = 'search_ride')
    {

        $user = auth()->user();
        $isGuest = !$user;
        $per_page = 6;
        $excludedDriverIds = $user ? $this->getTemporarilyBlockedDriverIds($user->id) : [];


        $searchFilters = $this->getPxSearchFilters($request);


        $searchFilters['proximalocal'] = 0;
        $page_type = 'px_ride';
        $action_route = 'search_ride';
        if ($view === 'search_folk_ride') {
            $searchFilters['extra_care'] = 1;
            $page_type = 'px_folk_ride';
            $action_route = 'folk_ride';
        } elseif ($view === 'search_pink_ride') {
            $searchFilters['women_only'] = 1;
            $page_type = 'px_pink_ride';
            $action_route = 'pink_ride';
        } elseif ($view === 'search_proximalocal_ride') {
            $searchFilters['proximalocal'] = 1;
            $page_type = 'px_proximalocal_ride';
            $action_route = 'proximalocal_ride';
        }

        $keyword = trim((string) $request->input('keyword'));
        $hasActiveFilters = collect($searchFilters)->contains(function ($value) {
            return $value !== null && $value !== '' && $value !== false;
        });

        // Check if there are search parameters
        $originLabel = trim((string) $request->input('origin.label'));
        $destinationLabel = trim((string) $request->input('destination.label'));
        $originCityId = $request->input('origin.city_id');
        $destinationCityId = $request->input('destination.city_id');
        $departureDate = $request->input('departure_date');
        $isSearchSubmission = $request->boolean('search');
        $hasSearch = false;
        $hasOriginSearch = $originLabel !== '' || !empty($originCityId);
        $hasDestinationSearch = $destinationLabel !== '' || !empty($destinationCityId);
        $hasLocationSearch = $hasOriginSearch || $hasDestinationSearch;
        $hasKeywordSearch = $keyword !== '';
        $hasPrimarySearch = $hasLocationSearch || $hasKeywordSearch;
        $shouldRunFilteredSearch = $hasPrimarySearch || $hasActiveFilters;

        $filters = array_merge($searchFilters, [
            'keyword' => $keyword,
            'per_page' => $per_page,
            'sort' => $isGuest && !$isSearchSubmission ? 'soonest' : 'latest_added',
            'excluded_driver_ids' => $excludedDriverIds,
            'require_vehicle' => true,
            'exclude_admin_deactive' => true,
        ]);

        if ($hasOriginSearch) {
            $filters['origin_city_id'] = $originCityId;
            $filters['origin_label'] = $originLabel;
        }

        if ($hasDestinationSearch) {
            $filters['destination_city_id'] = $destinationCityId;
            $filters['destination_label'] = $destinationLabel;
        }

        if (!empty($departureDate)) {
            $filters['departure_date'] = $departureDate;
        }

        $rides = Ride::searchRides(
            $shouldRunFilteredSearch ? $filters : array_diff_key($filters, array_flip([
                'origin_city_id',
                'origin_label',
                'destination_city_id',
                'destination_label',
                'departure_date',
            ])),
            $user
        );

        if ($isSearchSubmission) {
            $validator = Validator::make($request->all(), [
                'origin.label' => ['nullable', 'string', 'max:160'],
                'destination.label' => ['nullable', 'string', 'max:160'],
                'departure_date' => ['nullable', 'date'],
                'origin.city_id' => ['nullable', 'integer'],
                'destination.city_id' => ['nullable', 'integer'],
            ]);

            $invalidCityMessage = $this->siteText['invalid_city_error_text']
                ?? 'Please select a valid city from the dropdown.';
            $requiredFieldMessage = $this->siteText['required_field_error_text']
                ?? 'This field is required.';

            $validator->after(function ($validator) use ($request, $invalidCityMessage, $requiredFieldMessage, $hasActiveFilters) {
                $originLabel = trim((string) $request->input('origin.label'));
                $destinationLabel = trim((string) $request->input('destination.label'));
                $keyword = trim((string) $request->input('keyword'));
                $originCityId = $request->input('origin.city_id');
                $destinationCityId = $request->input('destination.city_id');
                $hasValidOrigin = $originLabel !== '' && !empty($originCityId);
                $hasValidDestination = $destinationLabel !== '' && !empty($destinationCityId);

                if ($keyword === '' && $originLabel === '' && $destinationLabel === '' && !$hasActiveFilters) {
                    $validator->errors()->add('keyword', 'Enter a keyword, select at least one valid city from the dropdown, or choose a filter.');
                }

                if ($keyword === '' && ($originLabel !== '' || $destinationLabel !== '')) {
                    if ($hasValidOrigin && $destinationLabel === '') {
                        $validator->errors()->add('destination.label', $requiredFieldMessage);
                    }

                    if ($hasValidDestination && $originLabel === '') {
                        $validator->errors()->add('origin.label', $requiredFieldMessage);
                    }
                }

                if ($originLabel !== '' && empty($originCityId)) {
                    $validator->errors()->add('origin.label', $invalidCityMessage);
                }

                if ($destinationLabel !== '' && empty($destinationCityId)) {
                    $validator->errors()->add('destination.label', $invalidCityMessage);
                }
            });

            $validator->validate();
        }

        if ($shouldRunFilteredSearch) {
            $hasSearch = true;

            if ($user && $hasPrimarySearch) {
                if ($user->suspand === '1') {
                    return redirect()
                        ->route('home', ['lang' => optional($this->selectedLanguage)->abbreviation])
                        ->with(['message' => 'Your account has been suspended by the admin']);
                }

                if ($keyword === '') {
                    $existingSearch = RecentSearch::query()
                        ->where('user_id', $user->id)
                        ->where('page_type', $page_type)
                        ->where('from', 'like', '%' . $originLabel . '%')
                        ->where('to', 'like', '%' . $destinationLabel . '%')
                        ->first();

                    if ($existingSearch) {
                        $existingSearch->touch();
                    } else {
                        RecentSearch::create([
                            'from' => $originLabel,
                            'to' => $destinationLabel,
                            'from_city_id' => $originCityId,
                            'to_city_id' => $destinationCityId,
                            'user_id' => $user->id,
                            'page_type' => $page_type,
                        ]);
                    }
                }
            }
        }
        foreach ($rides as $ride) {

            $ride = $this->getRideDetail($ride, $originLabel, $destinationLabel, $originCityId, $destinationCityId);

            // $stopsSource = $ride->stops ?? $ride->rideStops ?? null;
            // $orderedStops = $stopsSource
            //     ? $stopsSource->sortBy('stop_order')->values()->all()
            //     : [];

            // if ($hasLocationSearch) {
            //     [$matchedFromIndex, $matchedToIndex] = $this->findMatchingSegmentIndices(
            //         $orderedStops,
            //         $originCityId,
            //         $destinationCityId,
            //         (string) $originLabel,
            //         (string) $destinationLabel
            //     );
            // } else {
            //     $matchedFromIndex = count($orderedStops) >= 2 ? 0 : null;
            //     $matchedToIndex = count($orderedStops) >= 2 ? count($orderedStops) - 1 : null;
            // }

            // $ride->matched_from_stop_index = $matchedFromIndex;
            // $ride->matched_to_stop_index = $matchedToIndex;
            // $ride->matched_from_stop_id = ($matchedFromIndex !== null && isset($orderedStops[$matchedFromIndex]))
            //     ? (int) ($orderedStops[$matchedFromIndex]->id ?? 0)
            //     : null;
            // $ride->matched_to_stop_id = ($matchedToIndex !== null && isset($orderedStops[$matchedToIndex]))
            //     ? (int) ($orderedStops[$matchedToIndex]->id ?? 0)
            //     : null;
            // $ride->matched_segment_price_minor = $this->resolveMatchedSegmentPriceMinor(
            //     $ride,
            //     $originCityId,
            //     $destinationCityId,
            //     (string) $originLabel,
            //     (string) $destinationLabel,
            //     $matchedFromIndex,
            //     $matchedToIndex
            // );

            // $ride->matched_seats_available = ($ride->matched_from_stop_id && $ride->matched_to_stop_id && method_exists($ride, 'resolveSegmentAvailableSeats'))
            //     ? $ride->resolveSegmentAvailableSeats(
            //         (int) $ride->matched_from_stop_id,
            //         (int) $ride->matched_to_stop_id
            //     )
            //     : (int) ($ride->seats_available ?? $ride->seats ?? 0);

            
        }

        $recentSearches = collect();
        if ($user) {
            $recentSearches = RecentSearch::query()
                ->where('user_id', $user->id)
                ->where('page_type', $page_type)
                ->where('from', '!=', '')
                ->where('to', '!=', '')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get()
                ->map(function (RecentSearch $recentSearch) use ($lang, $view) {
                    $originLabel = trim((string) $recentSearch->from);
                    $destinationLabel = trim((string) $recentSearch->to);
                    $originCityId = $this->resolveRecentSearchCityId($originLabel);
                    $destinationCityId = $this->resolveRecentSearchCityId($destinationLabel);

                    $recentSearch->origin_label = $originLabel;
                    $recentSearch->destination_label = $destinationLabel;
                    $recentSearch->origin_city_id = $originCityId;
                    $recentSearch->destination_city_id = $destinationCityId;
                    $recentSearch->search_url = ($originCityId && $destinationCityId)
                        ? route($view, [
                            'lang' => $lang ?? optional($this->selectedLanguage)->abbreviation,
                            'origin' => [
                                'label' => $originLabel,
                                'city_id' => $originCityId,
                            ],
                            'destination' => [
                                'label' => $destinationLabel,
                                'city_id' => $destinationCityId,
                            ],
                            'search' => 1,
                        ])
                        : null;

                    return $recentSearch;
                })
                ->filter(fn(RecentSearch $recentSearch) => !empty($recentSearch->search_url))
                ->values();
        }

        $findRidePage = FindRidePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $rideDetailPage = RideDetailPageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);
        $homePage = HomePageSettingDetail::getByLanguageWithFallback($this->selectedLanguage->id, $this->defaultLang->id);

        $firm_cancellation_discount = SiteSetting::value('frim_discount');

        View::share([
            'findRidePage' => $findRidePage,
            'rideDetailPage' => $rideDetailPage,
            'homePage' => $homePage,
            'firm_cancellation_discount' => $firm_cancellation_discount,
        ]);

        return view($view, [
            'action_route' => $action_route,
            
            'rides' => $rides,
            'recentSearches' => $recentSearches,
            'hasSearch' => $hasSearch,
            'hasActiveFilters' => $hasActiveFilters,
            'searchFilters' => $searchFilters,
            'oldOriginLabel' => old('origin.label', $originLabel),
            'oldOriginCityId' => old('origin.city_id', $request->input('origin.city_id')),
            'oldDestinationLabel' => old('destination.label', $destinationLabel),
            'oldDestinationCityId' => old('destination.city_id', $request->input('destination.city_id')),
            'oldDepartureDate' => old('departure_date', $departureDate),
            'oldKeyword' => old('keyword', $request->input('keyword')),
        ]);
    }

    protected function getPassengerVehicles()
    {
        return Vehicle::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('primary_vehicle')
            ->orderByDesc('id')
            ->get();
    }



    protected function redirectPxMyRidesError(string $message)
    {
        return redirect()
            ->route('px.my_rides', ['lang' => optional($this->selectedLanguage)->abbreviation])
            ->with('error', $message);
    }

    protected function redirectPxMyRideDetailError(int $rideId, string $message)
    {
        return redirect()
            ->route('px.my_ride_detail', ['lang' => optional($this->selectedLanguage)->abbreviation, 'id' => $rideId])
            ->with('error', $message);
    }

    protected function redirectPxSearchRideError(string $message)
    {
        return redirect()
            ->route('search_ride', ['lang' => optional($this->selectedLanguage)->abbreviation])
            ->with('error', $message);
    }




    
}
