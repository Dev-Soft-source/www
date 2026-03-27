<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\SiteSetting;
use App\Support\LocationCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Traits\StatusResponser;

class CountryStateCityController extends Controller
{
    use StatusResponser;

    public function getCountries()
    {
        $countries = Cache::rememberForever(
            LocationCache::key('api:locations:countries:all'),
            fn () => Country::orderBy('name', 'asc')->get()
        );


        $data = ['countries' => $countries];
        return $this->successResponse($data, 'Get data successfully');
    }

    public function getStates(Request $request)
    {
        $countryId = (int) $request->country_id;
        $cacheKey = LocationCache::key('api:locations:states:country:' . $countryId);
        $states = Cache::rememberForever(
            $cacheKey,
            fn () => State::where('country_id', $countryId)->orderBy('name', 'asc')->get()
        );

        $data = ['states' => $states];
        return $this->successResponse($data, 'Get data successfully');
    }

    public function getCities(Request $request)
    {
        $stateId = (int) ($request->state_id ?? 0);
        $search = trim((string) ($request->search ?? ''));
        $normalizedSearch = mb_strtolower($search);
        $cacheKey = LocationCache::key('api:locations:cities:state:' . $stateId . ':search:' . md5($normalizedSearch));

        $cities = Cache::rememberForever(
            $cacheKey,
            function () use ($stateId, $search) {
                $citiesQuery = City::with(['state:id,abrv,country_id', 'state.country:id,name']);

                if ($stateId !== 0) {
                    $citiesQuery = $citiesQuery->where('state_id', $stateId);
                }

                if ($search !== '') {
                    $citiesQuery = $citiesQuery->where('name', 'like', $search . '%');
                }

                return $citiesQuery->orderBy('name', 'asc')->select('id', 'name', 'state_id')->get();
            }
        );

        $data = ['cities' => $cities];
        return $this->successResponse($data, 'Get data successfully');
    }

    public function getCityDistance(Request $request)
    {
        $distance = 0;
        $googleApiData = $this->getDataFromGoogleApi($request->search, $request->searchData);
        if (isset($googleApiData) && !empty($googleApiData)) {

            $distance = isset($googleApiData['rows']) && isset($googleApiData['rows'][0]) && isset($googleApiData['rows'][0]['elements']) && isset($googleApiData['rows'][0]['elements'][0]) && isset($googleApiData['rows'][0]['elements'][0]['distance']) ? $googleApiData['rows'][0]['elements'][0]['distance']['value'] : 0;
        }

        if ($distance != 0) {
            $distance = round(($distance / 1000), 2);
        }

        $siteSetting = SiteSetting::getCached();

        $pricePerKm = $siteSetting->price_per_km;

        $pricePerKm = isset($pricePerKm) ? $pricePerKm : 0;

        $pricePerKm = $pricePerKm * $distance;

        $data['pricePerKm'] = round($pricePerKm, 2);
        return $this->successResponse($data, 'Get data successfully');
    }

    public function getDataFromGoogleApi($from, $to)
    {

        $apiKey = env('GOOGLE_API_KEY');
        $ch = curl_init();

        $from = str_replace(" ", "", $from);
        $to = str_replace(" ", "", $to);

        curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $from . "&destinations=" . $to . "&units=imperial&key=" . $apiKey . "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        }

        curl_close($ch);

        $data = json_decode($response, true);

        return $data;
    }
}
