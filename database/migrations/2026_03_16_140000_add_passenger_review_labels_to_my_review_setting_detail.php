<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->string('passenger_review_heading')->nullable()->after('review_label');
            $table->string('passenger_review_criteria_heading')->nullable()->after('passenger_review_heading');
            $table->string('passenger_review_condition_label')->nullable()->after('passenger_review_criteria_heading');
            $table->string('passenger_review_conscious_label')->nullable()->after('passenger_review_condition_label');
            $table->string('passenger_review_comfort_label')->nullable()->after('passenger_review_conscious_label');
            $table->string('passenger_review_communication_label')->nullable()->after('passenger_review_comfort_label');
            $table->string('passenger_review_attitude_label')->nullable()->after('passenger_review_communication_label');
            $table->string('passenger_review_hygiene_label')->nullable()->after('passenger_review_attitude_label');
            $table->string('passenger_review_respect_label')->nullable()->after('passenger_review_hygiene_label');
            $table->string('passenger_review_safety_label')->nullable()->after('passenger_review_respect_label');
            $table->string('passenger_review_timeliness_label')->nullable()->after('passenger_review_safety_label');
        });
    }

    public function down()
    {
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'passenger_review_heading',
                'passenger_review_criteria_heading',
                'passenger_review_condition_label',
                'passenger_review_conscious_label',
                'passenger_review_comfort_label',
                'passenger_review_communication_label',
                'passenger_review_attitude_label',
                'passenger_review_hygiene_label',
                'passenger_review_respect_label',
                'passenger_review_safety_label',
                'passenger_review_timeliness_label',
            ]);
        });
    }
};
