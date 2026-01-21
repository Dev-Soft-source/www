<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\SiteSetting;
use App\Services\CountryStateCityApiService;
use Illuminate\Http\Request;

class CountryStateCityController extends Controller
{
    protected $apiService;

    public function __construct(CountryStateCityApiService $apiService)
    {
        $this->apiService = $apiService;
    }
    public function getState(Request $request)
    {
        $data['states'] = State::where("country_id",$request->country_id)->where('status', '1')
                    ->orderBy("name", "asc")->get(["name","id"]);
        return response()->json($data);
    }

    public function getCity(Request $request)
    {
        $cities = City::with(['state:id,abrv,country_id', 'state.country:id,name'])->where('status', '1');

        if(isset($request->state_id) && $request->state_id != 0){
            $cities = $cities->where('state_id', $request->state_id);
        }

        if(isset($request->search) && $request->search != ""){
            $cities = $cities->where('name', 'like', $request->search.'%');
        }

        // Group by city name, state_id to remove database-level duplicates before processing
        $cities = $cities->orderBy('name', 'asc')
            ->get()
            ->unique(function ($city) {
                // Use name and state_id as unique key at database level
                return strtolower(trim($city->name)) . '|' . ($city->state_id ?? 'null');
            });

        // Deduplicate cities to ensure only one entry per unique city name + state + country combination
        // This prevents showing "Ottawa, ON, Canada" and multiple "Ottawa, null, United States" entries
        // Special handling: If state abbreviation is null, group by base_name + country_id only
        // Otherwise, group by base_name + state_id + country_id
        $uniqueCities = $cities->map(function ($city) {
            // Extract the base city name by removing any state/country suffixes
            // Pattern: "City Name, State, Country" or "City Name, State" -> "City Name"
            $baseName = trim(preg_replace('/\s*,\s*[^,]+(?:,\s*[^,]+)?$/', '', $city->name));
            $countryId = $city->state && $city->state->country ? $city->state->country->id : null;
            $stateAbrv = $city->state ? $city->state->abrv : null;
            return [
                'city' => $city,
                'base_name' => strtolower(trim($baseName)),
                'state_id' => $city->state_id,
                'state_abrv' => $stateAbrv,
                'country_id' => $countryId
            ];
        })->unique(function ($item) {
            // If state abbreviation is null, group only by base_name + country_id
            // This ensures multiple "Ottawa, null, United States" entries show as one
            // If state abbreviation exists, group by base_name + state_id + country_id
            if (empty($item['state_abrv'])) {
                return $item['base_name'] . '|' . ($item['country_id'] ?? 'null');
            } else {
                return $item['base_name'] . '|' . ($item['state_id'] ?? 'null') . '|' . ($item['country_id'] ?? 'null');
            }
        })->map(function ($item) {
            // Return only the city object, but ensure the name is clean (base name only)
            $city = $item['city'];
            // Update the city name to be just the base name (without state/country suffixes)
            $baseName = trim(preg_replace('/\s*,\s*[^,]+(?:,\s*[^,]+)?$/', '', $city->name));
            $city->name = $baseName;
            return $city;
        });

        // Reset array keys after unique() to ensure proper JSON encoding
        $data['cities'] = $uniqueCities->values();
        return response()->json($data);
    }

    public function getCityDistance(Request $request)
    {
        $distance= 0;
        $googleApiData = $this->getDataFromGoogleApi($request->searchData, $request->search);
        if(isset($googleApiData) && !empty($googleApiData)){
            
            $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
        }
        
        if($distance != 0){
            $distance = round(($distance / 1000), 2);
        }

        $siteSetting = SiteSetting::first();

        $pricePerKm = $siteSetting->price_per_km;

        $pricePerKm = isset($pricePerKm) ? $pricePerKm : 0;

        $pricePerKm = $pricePerKm * $distance; 

        $data['pricePerKm'] = round($pricePerKm, 2);
        $data['distance'] = $distance; // Return distance in kilometers for frontend validation
        return response()->json($data);
    }

    public function getDataFromGoogleApi($from, $to){

        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        $from = str_replace(" ", "", $from);
        $to = str_replace(" ", "", $to);

        curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$from."&destinations=".$to."&units=imperial&key=".$apiKey."");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        }

        curl_close($ch);

        $data = json_decode($response, true);


        return $data;
    }

    /**
     * Get states from API by country name
     * This uses the free API instead of database
     */
    public function getStatesFromApi(Request $request)
    {
        $countryName = $request->country_name;

        if (!$countryName) {
            return response()->json(['error' => 'Country name is required'], 400);
        }

        $states = $this->apiService->getStatesByCountry($countryName);

        return response()->json([
            'success' => true,
            'states' => $states
        ]);
    }

    /**
     * Get cities from API by country and state
     * This uses the free API instead of database
     */
    public function getCitiesFromApi(Request $request)
    {
        $countryName = $request->country_name;
        $stateName = $request->state_name;
        $search = $request->search ?? '';

        if (!$countryName || !$stateName) {
            return response()->json(['error' => 'Country and state names are required'], 400);
        }

        $cities = $this->apiService->getCitiesByState($countryName, $stateName);

        // Filter by search term if provided
        if ($search) {
            $cities = collect($cities)->filter(function ($city) use ($search) {
                return stripos($city['name'], $search) === 0;
            })->values()->toArray();
        }

        // Format cities similar to database response
        $formattedCities = collect($cities)->map(function ($city) use ($stateName, $countryName, $request) {
            // Get state code if we have it in database
            $stateCode = null;
            if ($request->state_id) {
                $state = State::find($request->state_id);
                $stateCode = $state ? $state->abrv : null;
            }

            return [
                'name' => $city['name'],
                'display_name' => $city['name'] . ($stateCode ? ', ' . $stateCode . ', ' . $countryName : ', ' . $countryName)
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'cities' => $formattedCities
        ]);
    }

    /**
     * Hybrid method - uses API if database is empty, otherwise uses database
     */
    public function getCityHybrid(Request $request)
    {
        // First try database
        $cities = City::with(['state:id,abrv,country_id', 'state.country:id,name'])->where('status', '1');

        if(isset($request->state_id) && $request->state_id != 0){
            $cities = $cities->where('state_id', $request->state_id);
        }

        if(isset($request->search) && $request->search != ""){
            $cities = $cities->where('name', 'like', $request->search.'%');
        }

        $cities = $cities->orderBy('name', 'asc')->get();

        // If no cities found in database and we have state info, try API
        if ($cities->isEmpty() && isset($request->state_id)) {
            $state = State::with('country')->find($request->state_id);

            if ($state && $state->country) {
                $apiCities = $this->apiService->getCitiesByState(
                    $state->country->name,
                    $state->name
                );

                // Filter by search if provided
                if (isset($request->search) && $request->search != "") {
                    $apiCities = collect($apiCities)->filter(function ($city) use ($request) {
                        return stripos($city['name'], $request->search) === 0;
                    })->values()->toArray();
                }

                // Format API cities to match database structure
                $formattedCities = collect($apiCities)->map(function ($city) use ($state) {
                    return (object)[
                        'id' => 'api_' . md5($city['name'] . $state->id), // Temporary ID for API cities
                        'name' => $city['name'],
                        'state_id' => $state->id,
                        'state' => (object)[
                            'id' => $state->id,
                            'abrv' => $state->abrv,
                            'country_id' => $state->country_id,
                            'country' => (object)[
                                'id' => $state->country->id,
                                'name' => $state->country->name
                            ]
                        ]
                    ];
                });

                return response()->json([
                    'cities' => $formattedCities,
                    'source' => 'api'
                ]);
            }
        }

        return response()->json([
            'cities' => $cities,
            'source' => 'database'
        ]);
    }
}
