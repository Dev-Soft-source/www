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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['name','email_verified_at']);
            $table->string('username')->after('id')->default('');
            $table->string('first_name')->after('username')->default('');
            $table->string('last_name')->after('first_name')->default('');
            $table->string('type')->after('last_name')->default('');
            $table->string('gender')->after('type')->default('');
            $table->string('country')->after('gender')->default('');
            $table->string('state')->after('country')->default('');
            $table->string('city')->after('state')->default('');
            $table->string('address')->after('city')->default('');
            $table->string('zipcode')->after('address')->default('');
            $table->string('email_verified')->after('email')->default('0');
            $table->string('verify')->after('password')->default('0');
            $table->string('profile_image')->after('verify')->default('');
            $table->string('phone')->after('profile_image')->default('');
            $table->string('phone_verified')->after('phone')->default('0');
            $table->string('dob')->after('phone_verified')->default('');
            $table->string('about')->after('dob')->default('');
            $table->string('smoke')->after('about')->default('');
            $table->string('pets')->after('smoke')->default('');
            $table->string('sms')->after('pets')->default('1');
            $table->string('emails')->after('sms')->default('1');
            $table->string('status')->after('emails')->default('0');
            $table->string('driver_liscense')->after('status')->default('');
            $table->string('student_card')->after('driver_liscense')->default('');
            $table->string('home_country')->after('student_card')->default('');
            $table->string('home_state')->after('home_country')->default('');
            $table->string('home_city')->after('home_state')->default('');
            $table->string('home_address')->after('home_city')->default('');
            $table->string('home_zipcode')->after('home_address')->default('');
            $table->string('country_code')->after('home_zipcode')->default('');
            $table->string('balance')->after('country_code')->default('0');
            $table->string('booking_price')->after('balance')->nullable();
            $table->string('booking_per')->after('booking_price')->nullable();
            $table->string('charge_booking')->after('booking_per')->default('1');
            $table->string('booking_credits')->after('charge_booking')->default('0');
            $table->string('referral')->after('booking_credits')->default('');
            $table->string('referral_bookings')->after('referral')->default('0');
            $table->string('free_rides')->after('referral_bookings')->default('');
            $table->string('welcome')->after('free_rides')->default('0');
            $table->string('driver')->after('welcome')->default('0');
            $table->string('student')->after('driver')->default('0');
            $table->string('features')->after('student')->default('');
            $table->string('step')->after('features')->default('1');
            $table->string('suspand')->after('step')->default('0');
            $table->string('deleted')->after('suspand')->default('0');
            $table->string('lang')->after('deleted')->default('en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('email_verified_at')->after('email');
            $table->dropColumn([
                'username','first_name','last_name','type','gender','country','state','city','address','zipcode',
                'verify','profile_image','phone','phone_verified','dob','about','smoke','pets','sms','emails',
                'status','driver_liscense','student_card','home_country','home_state','home_city','home_address',
                'home_zipcode','country_code','balance','booking_price','booking_per','charge_booking',
                'booking_credits','referral','referral_bookings','free_rides','welcome','driver','student',
                'features','step','suspand','deleted','lang'
            ]);
        });
    }
};
