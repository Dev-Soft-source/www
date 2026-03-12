<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryStateCityApiService
{
    /**
     * CountriesNow.space API - used when GeoNames is not configured
     */
    private $baseUrl = 'https://countriesnow.space/api/v0.1/countries';

    /**
     * GeoNames search API (industry standard). Set GEONAMES_USERNAME in .env for better city coverage.
     * Free registration: https://www.geonames.org/login
     */
    private function getGeonamesUsername(): ?string
    {
        $username = config('services.geonames.username') ?: env('GEONAMES_USERNAME');
        return $username ?: null;
    }

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
     * Get cities by state and country (tries GeoNames first if configured, then CountriesNow.space)
     *
     * @param string $countryName
     * @param string $stateName
     * @param string|null $countryIso ISO 3166-1 alpha-2 (e.g. US) for GeoNames
     * @param string|null $stateCode State/region code (e.g. NY) for GeoNames
     * @return array Array of ['name' => string]
     */
    public function getCitiesByState($countryName, $stateName, $countryIso = null, $stateCode = null)
    {
        $cacheKey = 'api_cities_' . md5($countryName . '_' . $stateName . '_' . ($countryIso ?? '') . '_' . ($stateCode ?? ''));

        return Cache::remember($cacheKey, 86400, function () use ($countryName, $stateName, $countryIso, $stateCode) {
            $username = $this->getGeonamesUsername();
            if ($username && $countryIso && $stateCode) {
                $fromGeonames = $this->getCitiesByStateFromGeoNames($countryIso, $stateCode, $username);
                if (!empty($fromGeonames)) {
                    return $fromGeonames;
                }
            }

            return $this->getCitiesByStateFromCountriesNow($countryName, $stateName);
        });
    }

    /**
     * GeoNames search API - populated places in a country/state (industry standard, full coverage)
     */
    public function getCitiesByStateFromGeoNames(string $countryIso, string $stateCode, string $username): array
    {
        $cacheKey = 'geonames_cities_' . md5(strtoupper($countryIso) . '_' . strtoupper($stateCode));

        return Cache::remember($cacheKey, 86400, function () use ($countryIso, $stateCode, $username) {
            try {
                $response = Http::timeout(15)->get('https://api.geonames.org/search', [
                    'country' => strtoupper($countryIso),
                    'adminCode1' => strtoupper($stateCode),
                    'featureClass' => 'P', // populated place
                    'type' => 'json',
                    'username' => $username,
                    'maxRows' => 1000,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $list = $data['geonames'] ?? [];
                    $names = collect($list)->pluck('name')->unique()->sort()->values();
                    return $names->map(fn ($name) => ['name' => $name])->toArray();
                }

                Log::warning('GeoNames cities response not successful', [
                    'country' => $countryIso,
                    'state' => $stateCode,
                    'status' => $response->status(),
                ]);
                return [];
            } catch (\Exception $e) {
                Log::error('Exception fetching cities from GeoNames', [
                    'country' => $countryIso,
                    'state' => $stateCode,
                    'error' => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    /**
     * CountriesNow.space API (fallback when GeoNames not used)
     */
    protected function getCitiesByStateFromCountriesNow(string $countryName, string $stateName): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/state/cities", [
                'country' => $countryName,
                'state' => $stateName,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data'])) {
                    return collect($data['data'])->map(fn ($city) => ['name' => is_array($city) ? ($city['name'] ?? $city) : $city])->toArray();
                }
            }

            Log::error('Failed to fetch cities from CountriesNow', [
                'country' => $countryName,
                'state' => $stateName,
                'response' => $response->body(),
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error('Exception fetching cities from CountriesNow', [
                'country' => $countryName,
                'state' => $stateName,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
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
