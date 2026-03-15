<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('driver_page_setting_detail', function (Blueprint $table) {
            $table->string('driver_info_heading')->nullable()->after('page_description');
            $table->string('joined_label')->nullable()->after('driver_info_heading');
            $table->string('age_label')->nullable()->after('joined_label');
            $table->string('mini_bio_heading')->nullable()->after('age_label');
            $table->string('passengers_driven_label')->nullable()->after('mini_bio_heading');
            $table->string('rides_taken_label')->nullable()->after('passengers_driven_label');
            $table->string('km_shared_label')->nullable()->after('rides_taken_label');
            $table->string('vehicle_info_heading')->nullable()->after('km_shared_label');
            $table->string('reviews_heading')->nullable()->after('vehicle_info_heading');
            $table->string('see_all_reviews_btn')->nullable()->after('reviews_heading');
        });
    }

    public function down()
    {
        Schema::table('driver_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'driver_info_heading',
                'joined_label',
                'age_label',
                'mini_bio_heading',
                'passengers_driven_label',
                'rides_taken_label',
                'km_shared_label',
                'vehicle_info_heading',
                'reviews_heading',
                'see_all_reviews_btn',
            ]);
        });
    }
};
