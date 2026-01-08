<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->string('luggage_option1')->after('luggage_placeholder')->nullable();
            $table->string('luggage_option2')->after('luggage_option1')->nullable();
            $table->string('luggage_option3')->after('luggage_option2')->nullable();
            $table->string('luggage_option4')->after('luggage_option3')->nullable();
            $table->string('luggage_option5')->after('luggage_option4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('find_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['luggage_option5','luggage_option4','luggage_option3','luggage_option2','luggage_option1']);
        });
    }
};
