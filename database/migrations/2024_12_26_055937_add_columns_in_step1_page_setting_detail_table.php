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
        Schema::table('step1_page_setting_detail', function (Blueprint $table) {
            $table->string('first_name_error')->nullable();
            $table->string('last_name_error')->nullable();
            $table->string('gender_error')->nullable();
            $table->string('dob_error')->nullable();
            $table->string('country_error')->nullable();
            $table->string('state_error')->nullable();
            $table->string('city_error')->nullable();
            $table->string('zip_code_error')->nullable();
            $table->string('bio_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('step1_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['bio_error', 'zip_code_error', 'city_error', 'state_error', 'country_error', 'dob_error',
                'gender_error', 'last_name_error', 'first_name_error'
            ]);
        });
    }
};
