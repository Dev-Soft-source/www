<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);

        if (Schema::hasTable('notification_messages') && Schema::hasTable('notification_message_details') && Schema::hasTable('languages')) {
            $this->call(NotificationMessageSeeder::class);
        }
    }
}
