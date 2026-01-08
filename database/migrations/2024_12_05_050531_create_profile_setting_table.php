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
        Schema::create('profile_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('profile_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_setting_id')
                ->constrained()
                ->on('profile_page_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('main_heading')->nullable();
            $table->string('profile_photo_label')->nullable();
            $table->string('my_vehicles_label')->nullable();
            $table->string('password_label')->nullable();
            $table->string('my_phone_number_label')->nullable();
            $table->string('my_email_address_label')->nullable();
            $table->string('my_driver_license_label')->nullable();
            $table->string('my_student_card_label')->nullable();
            $table->string('referrals_label')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_setting_detail');
        Schema::dropIfExists('profile_setting');
    }
};
