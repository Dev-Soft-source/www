<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RideTableSeeder extends Seeder
{
    public function run()
    {
        // Use the Canadian locale for Faker
        $faker = Faker::create('en_CA');

        // Seed rides table
        for ($i = 0; $i < 50; $i++) {
            $rideId = DB::table('rides')->insertGetId([
                'departure' => $faker->address,
                'departure_lat' => $faker->latitude,
                'departure_lng' => $faker->longitude,
                'destination' => $faker->address,
                'destination_lat' => $faker->latitude,
                'destination_lng' => $faker->longitude,
                'total_distance' => $faker->randomFloat(2, 10, 500),
                'total_time' => $faker->numberBetween(30, 600) . ' mins',
                'date' => $faker->dateBetween('+1 week', '+1 month')
                ,
                'time' => $faker->time(),
                'completed_date' => $faker->date(),
                'completed_time' => $faker->time(),
                'destination_reached_date' => $faker->date(),
                'destination_reached_time' => $faker->time(),
                'recurring' => '0',
                'recurring_type' => "",
                'recurring_trips' => "",
                'details' => $faker->text,
                'seats' => $faker->numberBetween(1, 6),
                'middle_seats' => $faker->randomElement(['2', '3']),
                'back_seats' => $faker->randomElement(['2', '3']),
                'max_back_seats' => '0',
                'make' => $faker->word,
                'model' => $faker->word,
                'vehicle_type' => $faker->randomElement(['38', '39', '40', '41', '42', '43']),
                'year' => $faker->year,
                'color' => $faker->safeColorName,
                'license_no' => $faker->bothify('???-###'),
                'car_type' => $faker->randomElement(['gas', 'hybird']),
                'smoke' => $faker->randomElement(['21', '22']),
                'animal_friendly' => $faker->randomElement(['23', '24', '25']),
                'features' => $faker->numberBetween(1, 20),
                'booking_method' => $faker->randomElement(['31', '32']),
                'price' => $faker->randomFloat(2, 10, 100),
                'payment_method' => $faker->randomElement(['33', '34', '35']),
                'luggage' => $faker->randomElement(['26', '27', '28', '29', '30']),
                'accept_more_luggage' => '0',
                'open_customized' => '0',
                'until_limit' => '0',
                'notes' => $faker->text,
                'added_by' => $faker->numberBetween(1, 10),
                'departure_place' => $faker->city,
                'departure_route' => $faker->streetName,
                'departure_zipcode' => $faker->postcode,
                'departure_city' => $faker->city,
                'departure_state' => $faker->state,
                'departure_country' => 'Canada', // Hardcoded for Canadian context
                'destination_place' => $faker->city,
                'destination_route' => $faker->streetName,
                'destination_zipcode' => $faker->postcode,
                'destination_city' => $faker->city,
                'destination_state' => $faker->state,
                'destination_country' => 'Canada', // Hardcoded for Canadian context
                'status' => '0',
                'pickup' => $faker->address,
                'dropoff' => $faker->address,
                'departure_state_short' => $faker->stateAbbr,
                'destination_state_short' => $faker->stateAbbr,
            ]);

            // Seed seat_details table for each ride
            for ($j = 0; $j < $faker->numberBetween(1, 6); $j++) {
                DB::table('seat_details')->insert([
                    'ride_id' => $rideId,
                    'seat_number' => $j + 1,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}