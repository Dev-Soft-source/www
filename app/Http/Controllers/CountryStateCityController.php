<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\SiteSetting;
use App\Services\CountryStateCityApiService;
use App\Support\LocationCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryStateCityController extends Controller
{
    protected $apiService;

    public function __construct(CountryStateCityApiService $apiService)
    {
        $this->apiService = $apiService;
    }
    public function getState(Request $request)
    {
        $countryId = (int) $request->country_id;
        $cacheKey = LocationCache::key('web:locations:states:country:' . $countryId);

        $data['states'] = Cache::rememberForever(
            $cacheKey,
            fn () => State::where('country_id', $countryId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get(['name', 'id'])
        );

        return response()->json($data);
    }

    public function getCity(Request $request)
    {
        $stateId = (int) ($request->state_id ?? 0);
        $search = trim((string) ($request->search ?? ''));
        $cacheKey = LocationCache::key('web:locations:cities:state:' . $stateId . ':search:' . md5(mb_strtolower($search)));

        $cityList = Cache::rememberForever(
            $cacheKey,
            function () use ($stateId, $search) {
                $cities = City::with(['state:id,abrv,country_id', 'state.country:id,name'])->where('status', '1');

                if ($stateId !== 0) {
                    $cities = $cities->where('state_id', $stateId);
                }

                if ($search !== '') {
                    $cities = $cities->where('name', 'like', $search . '%');
                }

                $cities = $cities->orderBy('name', 'asc')
                    ->get()
                    ->unique(function ($city) {
                        return strtolower(trim($city->name)) . '|' . ($city->state_id ?? 'null');
                    });

                $uniqueCities = $cities->map(function ($city) {
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
                    if (empty($item['state_abrv'])) {
                        return $item['base_name'] . '|' . ($item['country_id'] ?? 'null');
                    }

                    return $item['base_name'] . '|' . ($item['state_id'] ?? 'null') . '|' . ($item['country_id'] ?? 'null');
                })->map(function ($item) {
                    $city = $item['city'];
                    $baseName = trim(preg_replace('/\s*,\s*[^,]+(?:,\s*[^,]+)?$/', '', $city->name));
                    $city->name = $baseName;
                    return $city;
                });

                $cityList = $uniqueCities->values();

                if ($stateId > 0) {
                    $state = State::with('country')->find($stateId);
                    if ($state && $state->country) {
                        $countryIso = $state->country->iso_code ?? null;
                        $stateCode = $state->abrv ?? null;
                        $apiCities = $this->apiService->getCitiesByState(
                            $state->country->name,
                            $state->name,
                            $countryIso,
                            $stateCode
                        );
                        $existingNames = $cityList->map(function ($c) {
                            return strtolower(trim(preg_replace('/\s*,\s*[^,]+(?:,\s*[^,]+)?$/', '', $c->name)));
                        })->flip();
                        foreach ($apiCities as $row) {
                            $name = is_array($row) ? ($row['name'] ?? '') : (string) $row;
                            if ($name === '') {
                                continue;
                            }
                            $key = strtolower(trim($name));
                            if ($existingNames->has($key)) {
                                continue;
                            }
                            $existingNames->put($key, true);
                            $slug = preg_replace('/[^a-z0-9]+/i', '_', $name);
                            $slug = trim($slug, '_') ?: 'city';
                            $cityList->push((object) [
                                'id' => 'api_' . $slug,
                                'name' => $name,
                                'state_id' => $state->id,
                                'state' => (object) [
                                    'id' => $state->id,
                                    'abrv' => $state->abrv ?? null,
                                    'country_id' => $state->country_id,
                                    'country' => (object) [
                                        'id' => $state->country->id,
                                        'name' => $state->country->name,
                                    ],
                                ],
                            ]);
                        }
                        $cityList = $cityList->sortBy(function ($c) {
                            return strtolower(is_object($c) ? $c->name : ($c['name'] ?? ''));
                        })->values();
                    }
                }

                return $cityList;
            }
        );

        $data['cities'] = $cityList;
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

        $siteSetting = SiteSetting::getCached();

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

        $states = Cache::rememberForever(
            LocationCache::key('web:api:states:country:' . md5(mb_strtolower(trim((string) $countryName)))),
            fn () => $this->apiService->getStatesByCountry($countryName)
        );

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
        $search = trim((string) ($request->search ?? ''));

        if (!$countryName || !$stateName) {
            return response()->json(['error' => 'Country and state names are required'], 400);
        }

        $formattedCities = Cache::rememberForever(
            LocationCache::key(
                'web:api:cities:country:' . md5(mb_strtolower(trim((string) $countryName)))
                    . ':state:' . md5(mb_strtolower(trim((string) $stateName)))
                    . ':search:' . md5(mb_strtolower($search))
                    . ':state_id:' . (int) ($request->state_id ?? 0)
            ),
            function () use ($countryName, $stateName, $search, $request) {
                $cities = $this->apiService->getCitiesByState($countryName, $stateName);

                if ($search !== '') {
                    $cities = collect($cities)->filter(function ($city) use ($search) {
                        return stripos($city['name'], $search) === 0;
                    })->values()->toArray();
                }

                return collect($cities)->map(function ($city) use ($countryName, $request) {
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
            }
        );

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
        $stateId = (int) ($request->state_id ?? 0);
        $search = trim((string) ($request->search ?? ''));
        $cacheKey = LocationCache::key('web:hybrid:cities:state:' . $stateId . ':search:' . md5(mb_strtolower($search)));

        $result = Cache::rememberForever(
            $cacheKey,
            function () use ($stateId, $search) {
                $cities = City::with(['state:id,abrv,country_id', 'state.country:id,name'])->where('status', '1');

                if ($stateId !== 0) {
                    $cities = $cities->where('state_id', $stateId);
                }

                if ($search !== '') {
                    $cities = $cities->where('name', 'like', $search . '%');
                }

                $cities = $cities->orderBy('name', 'asc')->get();

                if ($cities->isEmpty() && $stateId > 0) {
                    $state = State::with('country')->find($stateId);

                    if ($state && $state->country) {
                        $apiCities = $this->apiService->getCitiesByState(
                            $state->country->name,
                            $state->name
                        );

                        if ($search !== '') {
                            $apiCities = collect($apiCities)->filter(function ($city) use ($search) {
                                return stripos($city['name'], $search) === 0;
                            })->values()->toArray();
                        }

                        $formattedCities = collect($apiCities)->map(function ($city) use ($state) {
                            return (object)[
                                'id' => 'api_' . md5($city['name'] . $state->id),
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

                        return [
                            'cities' => $formattedCities,
                            'source' => 'api'
                        ];
                    }
                }

                return [
                    'cities' => $cities,
                    'source' => 'database'
                ];
            }
        );

        return response()->json($result);
    }
}
