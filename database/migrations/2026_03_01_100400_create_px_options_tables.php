<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('px_ride_option_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();
            $table->boolean('is_checkbox')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('px_ride_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('px_ride_option_groups')->cascadeOnDelete();
            $table->string('code', 120)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['group_id', 'is_active', 'sort_order'], 'px_ride_options_group_active_sort_idx');
        });

        Schema::create('px_ride_option_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')->constrained('px_ride_options')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['option_id', 'language_id'], 'px_ride_option_lang_uq');
            $table->index(['language_id', 'label'], 'px_ride_option_lang_label_idx');
        });

        Schema::create('px_ride_option_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('px_rides')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('px_ride_options')->cascadeOnDelete();
            $table->json('value')->nullable();
            $table->timestamps();

            $table->unique(['ride_id', 'option_id'], 'px_ride_option_assignment_uq');
            $table->index(['option_id'], 'px_ride_option_assignment_option_idx');
        });

        $this->seedDefaultOptions();
    }

    public function down(): void
    {
        Schema::dropIfExists('px_ride_option_assignments');
        Schema::dropIfExists('px_ride_option_translations');
        Schema::dropIfExists('px_ride_options');
        Schema::dropIfExists('px_ride_option_groups');
    }

    protected function seedDefaultOptions(): void
    {
        $groups = [
            ['code' => 'preference', 'is_checkbox' => 1, 'sort_order' => 10],
            ['code' => 'cancelation_policy', 'is_checkbox' => 0, 'sort_order' => 20],
            ['code' => 'pets_allowed', 'is_checkbox' => 0, 'sort_order' => 30],
            ['code' => 'smoking_allowed', 'is_checkbox' => 0, 'sort_order' => 40],
            ['code' => 'luggage_size', 'is_checkbox' => 0, 'sort_order' => 50],
            ['code' => 'booking_mode', 'is_checkbox' => 0, 'sort_order' => 60],
            ['code' => 'booking_method', 'is_checkbox' => 0, 'sort_order' => 70],
        ];

        DB::table('px_ride_option_groups')->insert(array_map(function ($g) {
            return array_merge($g, ['created_at' => now(), 'updated_at' => now()]);
        }, $groups));

        $groupMap = DB::table('px_ride_option_groups')->pluck('id', 'code');

        $options = [
            ['group' => 'preference', 'code' => 'pink_rides', 'label' => 'Pink Rides', 'sort' => 10, 'meta' => null],
            ['group' => 'preference', 'code' => 'extra_plus_rides', 'label' => 'Extra+ Rides', 'sort' => 20, 'meta' => null],
            ['group' => 'preference', 'code' => 'wifi', 'label' => 'Wi-Fi', 'sort' => 10, 'meta' => null],
            ['group' => 'preference', 'code' => 'heating', 'label' => 'Heating', 'sort' => 20, 'meta' => null],
            ['group' => 'preference', 'code' => 'air_conditioning', 'label' => 'Air-conditioning', 'sort' => 30, 'meta' => null],
            ['group' => 'preference', 'code' => 'child_passenger_own_baby_seat', 'label' => 'I welcome infants on board, but please note that passengers must provide their own car baby seats.', 'sort' => 10, 'meta' => null],
            ['group' => 'preference', 'code' => 'child_booster_on_request', 'label' => 'I accommodate infants and provide car booster seats upon request for a safe and comfortable ride', 'sort' => 20, 'meta' => null],
            ['group' => 'preference', 'code' => 'child_no_booster_passenger_provides', 'label' => 'I accommodate children but I do not provide car booster seats; the passenger must provide them', 'sort' => 30, 'meta' => null],
            ['group' => 'preference', 'code' => 'child_booster_provided', 'label' => 'I take children, and I provide car booster seats', 'sort' => 40, 'meta' => null],
            ['group' => 'preference', 'code' => 'bike_rack', 'label' => 'Bike-rack', 'sort' => 10, 'meta' => null],
            ['group' => 'preference', 'code' => 'ski_rack', 'label' => 'Ski-rack', 'sort' => 20, 'meta' => null],
            ['group' => 'preference', 'code' => 'winter_tires', 'label' => 'Winter tires', 'sort' => 30, 'meta' => null],
            ['group' => 'preference', 'code' => 'min_rating_5', 'label' => 'I only accept bookings from passengers with a 5-star rating', 'sort' => 10, 'meta' => json_encode(['min_rating' => 5])],
            ['group' => 'preference', 'code' => 'min_rating_4', 'label' => 'I only accept bookings from passengers with a 4-star rating and above', 'sort' => 20, 'meta' => json_encode(['min_rating' => 4])],
            ['group' => 'preference', 'code' => 'min_rating_3', 'label' => 'I only accept bookings from passengers with a 3-star rating and above', 'sort' => 30, 'meta' => json_encode(['min_rating' => 3])],
            ['group' => 'preference', 'code' => 'existing_reviews_only', 'label' => 'Only passengers with existing reviews are accepted; i.e. no new passengers', 'sort' => 40, 'meta' => json_encode(['requires_reviews' => true])],
            ['group' => 'preference', 'code' => 'verified_phone_only', 'label' => 'I only accept passengers with a verified phone', 'sort' => 50, 'meta' => json_encode(['requires_verified_phone' => true])],
            
            ['group' => 'cancelation_policy', 'code' => 'standard_cancellation', 'label' => 'Standard Cancellation', 'sort' => 50, 'meta' => null],
            ['group' => 'cancelation_policy', 'code' => 'firm_cancellation', 'label' => 'Firm Cancellation', 'sort' => 100, 'meta' => null],
            
            ['group' => 'pets_allowed', 'code' => 'pet_allowed', 'label' => 'Yes', 'sort' => 10, 'meta' => null],
            ['group' => 'pets_allowed', 'code' => 'pet_not_allowed', 'label' => 'No', 'sort' => 20, 'meta' => null],
            ['group' => 'pets_allowed', 'code' => 'caged_animals_only', 'label' => 'Caged animals only', 'sort' => 30, 'meta' => null],
            
            ['group' => 'smoking_allowed', 'code' => 'allowed', 'label' => 'Yes', 'sort' => 10, 'meta' => null],
            ['group' => 'smoking_allowed', 'code' => 'not_allowed', 'label' => 'No', 'sort' => 20, 'meta' => null],
           
            ['group' => 'luggage_size', 'code' => 'no_luggage', 'label' => 'No Luggage', 'sort' => 10, 'meta' => null],
            ['group' => 'luggage_size', 'code' => 'small', 'label' => 'Small', 'sort' => 20, 'meta' => null],
            ['group' => 'luggage_size', 'code' => 'medium', 'label' => 'Medium', 'sort' => 30, 'meta' => null],
            ['group' => 'luggage_size', 'code' => 'large', 'label' => 'Large', 'sort' => 40, 'meta' => null],
            ['group' => 'luggage_size', 'code' => 'xl_multiple', 'label' => 'XL and Multiple', 'sort' => 50, 'meta' => null],
            
            ['group' => 'booking_mode', 'code' => 'manual', 'label' => 'Manual Booking', 'sort' => 10, 'meta' => null],
            ['group' => 'booking_mode', 'code' => 'instant', 'label' => 'Instant Booking', 'sort' => 20, 'meta' => null],
            
            ['group' => 'booking_method', 'code' => 'cash', 'label' => 'Cash', 'sort' => 10, 'meta' => null],
            ['group' => 'booking_method', 'code' => 'online_payment', 'label' => 'Online Payment', 'sort' => 20, 'meta' => null],
            ['group' => 'booking_method', 'code' => 'secured_cash', 'label' => 'Secured-Cash', 'sort' => 30, 'meta' => null],

        ];

        $optionRows = [];
        foreach ($options as $opt) {
            $optionRows[] = [
                'group_id' => $groupMap[$opt['group']] ?? null,
                'code' => $opt['code'],
                'is_active' => 1,
                'sort_order' => $opt['sort'],
                'meta' => $opt['meta'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('px_ride_options')->insert($optionRows);

        $optionMap = DB::table('px_ride_options')->pluck('id', 'code');
        $languageIds = DB::table('languages')->pluck('id');

        $translations = [];
        foreach ($options as $opt) {
            $optionId = $optionMap[$opt['code']] ?? null;
            if (!$optionId) {
                continue;
            }
            foreach ($languageIds as $languageId) {
                $translations[] = [
                    'option_id' => $optionId,
                    'language_id' => $languageId,
                    'label' => $opt['label'],
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($translations)) {
            DB::table('px_ride_option_translations')->insert($translations);
        }
    }
};
