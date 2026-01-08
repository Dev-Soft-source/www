<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryStateCityApiService
{
    /**
     * API endpoints
     * Using CountryStateCity API - Free tier available
     * Alternative: https://countriesnow.space/api/v0.1/countries/states
     */
    private $baseUrl = 'https://countriesnow.space/api/v0.1/countries';

    /**
     * Get all countries
     *
     * @return array
     */
    public function getCountries()
    {
        return Cache::remember('api_countries', 86400, function () {
            try {
                $response = Http::timeout(10)->get("{$this->baseUrl}");

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['data'])) {
                        return collect($data['data'])->map(function ($country) {
                            return [
                                'name' => $country['country'],
                                'iso2' => $country['iso2'] ?? null,
                                'iso3' => $country['iso3'] ?? null,
                            ];
                        })->toArray();
                    }
                }

                Log::error('Failed to fetch countries from API', ['response' => $response->body()]);
                return [];
            } catch (\Exception $e) {
                Log::error('Exception fetching countries', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get states by country
     *
     * @param string $countryName
     * @return array
     */
    public function getStatesByCountry($countryName)
    {
        $cacheKey = 'api_states_' . md5($countryName);

        return Cache::remember($cacheKey, 86400, function () use ($countryName) {
            try {
                $response = Http::timeout(10)->post("{$this->baseUrl}/states", [
                    'country' => $countryName
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['data']['states'])) {
                        return collect($data['data']['states'])->map(function ($state) {
                            return [
                                'name' => $state['name'],
                                'state_code' => $state['state_code'] ?? null,
                            ];
                        })->toArray();
                    }
                }

                Log::error('Failed to fetch states from API', [
                    'country' => $countryName,
                    'response' => $response->body()
                ]);
                return [];
            } catch (\Exception $e) {
                Log::error('Exception fetching states', [
                    'country' => $countryName,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Get cities by state and country
     *
     * @param string $countryName
     * @param string $stateName
     * @return array
     */
    public function getCitiesByState($countryName, $stateName)
    {
        $cacheKey = 'api_cities_' . md5($countryName . '_' . $stateName);

        return Cache::remember($cacheKey, 86400, function () use ($countryName, $stateName) {
            try {
                $response = Http::timeout(10)->post("{$this->baseUrl}/state/cities", [
                    'country' => $countryName,
                    'state' => $stateName
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['data'])) {
                        return collect($data['data'])->map(function ($city) {
                            return ['name' => $city];
                        })->toArray();
                    }
                }

                Log::error('Failed to fetch cities from API', [
                    'country' => $countryName,
                    'state' => $stateName,
                    'response' => $response->body()
                ]);
                return [];
            } catch (\Exception $e) {
                Log::error('Exception fetching cities', [
                    'country' => $countryName,
                    'state' => $stateName,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Search cities by partial name (fallback to database if API doesn't support)
     *
     * @param string $searchTerm
     * @param string|null $countryName
     * @param string|null $stateName
     * @return array
     */
    public function searchCities($searchTerm, $countryName = null, $stateName = null)
    {
        if (!$countryName || !$stateName) {
            return [];
        }

        $cities = $this->getCitiesByState($countryName, $stateName);

        return collect($cities)->filter(function ($city) use ($searchTerm) {
            return stripos($city['name'], $searchTerm) === 0;
        })->values()->toArray();
    }

    /**
     * Clear all cached API data
     *
     * @return void
     */
    public function clearCache()
    {
        Cache::forget('api_countries');
        // Note: Individual state/city caches will expire after 24 hours
        Log::info('API cache cleared');
    }
}
