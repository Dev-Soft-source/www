<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Services\CountryStateCityApiService;
use Illuminate\Console\Command;

class SyncCitiesFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:sync
                            {--country= : Specific country name to sync}
                            {--state= : Specific state name to sync}
                            {--fresh : Clear existing cities before syncing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync cities from free API into database';

    protected $apiService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CountryStateCityApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting city sync from API...');

        if ($this->option('fresh')) {
            if ($this->confirm('This will delete all existing cities. Continue?')) {
                City::truncate();
                $this->info('Cleared existing cities');
            } else {
                return 0;
            }
        }

        $countryName = $this->option('country');
        $stateName = $this->option('state');

        if ($countryName && $stateName) {
            // Sync specific state
            $this->syncSpecificState($countryName, $stateName);
        } elseif ($countryName) {
            // Sync all states for a country
            $this->syncCountry($countryName);
        } else {
            // Sync all countries
            $this->syncAllCountries();
        }

        $this->info('✓ City sync completed!');
        return 0;
    }

    /**
     * Sync cities for a specific state
     */
    protected function syncSpecificState($countryName, $stateName)
    {
        $country = Country::where('name', $countryName)->first();
        if (!$country) {
            $this->error("Country '$countryName' not found in database");
            return;
        }

        $state = State::where('name', $stateName)
                     ->where('country_id', $country->id)
                     ->first();

        if (!$state) {
            $this->error("State '$stateName' not found in database");
            return;
        }

        $this->info("Fetching cities for {$stateName}, {$countryName}...");
        $cities = $this->apiService->getCitiesByState($countryName, $stateName);

        $this->syncCitiesForState($state, $cities);
    }

    /**
     * Sync all states for a country
     */
    protected function syncCountry($countryName)
    {
        $country = Country::where('name', $countryName)->first();
        if (!$country) {
            $this->error("Country '$countryName' not found in database");
            return;
        }

        $states = State::where('country_id', $country->id)->get();

        $this->info("Syncing cities for {$states->count()} states in {$countryName}...");

        $progressBar = $this->output->createProgressBar($states->count());
        $progressBar->start();

        foreach ($states as $state) {
            $cities = $this->apiService->getCitiesByState($countryName, $state->name);
            $this->syncCitiesForState($state, $cities);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Sync all countries
     */
    protected function syncAllCountries()
    {
        $countries = Country::with('states')->get();

        $this->info("Syncing cities for {$countries->count()} countries...");

        foreach ($countries as $country) {
            if ($country->states->isEmpty()) {
                continue;
            }

            $this->info("Processing {$country->name}...");

            foreach ($country->states as $state) {
                $cities = $this->apiService->getCitiesByState($country->name, $state->name);
                $this->syncCitiesForState($state, $cities);
            }
        }
    }

    /**
     * Sync cities for a specific state
     */
    protected function syncCitiesForState($state, $cities)
    {
        $inserted = 0;
        $updated = 0;

        foreach ($cities as $cityData) {
            $city = City::where('name', $cityData['name'])
                       ->where('state_id', $state->id)
                       ->first();

            if ($city) {
                // Update existing city
                $city->update(['status' => 1]);
                $updated++;
            } else {
                // Create new city
                City::create([
                    'name' => $cityData['name'],
                    'state_id' => $state->id,
                    'status' => 1
                ]);
                $inserted++;
            }
        }

        if ($inserted > 0 || $updated > 0) {
            $this->line("  {$state->name}: +{$inserted} new, ~{$updated} updated");
        }
    }
}
