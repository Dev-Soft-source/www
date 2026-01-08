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
        Schema::create('edit_profile_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edit_profile_id')
                ->constrained()
                ->on('edit_profile_page_setting')
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
            $table->string('name')->nullable();
            $table->string('min_bio_label')->nullable();
            $table->string('passenger_driven_label')->nullable();
            $table->string('rides_taken_label')->nullable();
            $table->string('km_shared_label')->nullable();
            $table->string('review_label')->nullable();
            $table->string('reply_label')->nullable();
            $table->string('link_review_label')->nullable();
            $table->string('review_heading')->nullable();
            $table->string('edit_profile_text')->nullable();
            $table->string('first_name_label')->nullable();
            $table->string('first_name_placeholder')->nullable();
            $table->string('last_name_label')->nullable();
            $table->string('last_name_placeholder')->nullable();
            $table->string('gender_label')->nullable();
            $table->string('male_label')->nullable();
            $table->string('female_label')->nullable();
            $table->string('dob_label')->nullable();
            $table->string('dob_placeholder')->nullable();
            $table->string('country_label')->nullable();
            $table->string('country_placeholder')->nullable();
            $table->string('state_label')->nullable();
            $table->string('state_placeholder')->nullable();
            $table->string('city_label')->nullable();
            $table->string('city_placeholder')->nullable();
            $table->string('address_label')->nullable();
            $table->string('address_placeholder')->nullable();
            $table->string('zip_label')->nullable();
            $table->string('mini_bio_label')->nullable();
            $table->string('mini_bio_placeholder')->nullable();
            $table->string('govt_id_label')->nullable();
            $table->string('image_placeholder')->nullable();
            $table->string('choose_file_placeholder')->nullable();
            $table->string('image_option_placeholder')->nullable();
            $table->string('new_image_button_text')->nullable();
            $table->string('save_button_text')->nullable();
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
        Schema::dropIfExists('edit_profile_page_setting_detail');
        Schema::dropIfExists('edit_profile_page_setting');
    }
};
