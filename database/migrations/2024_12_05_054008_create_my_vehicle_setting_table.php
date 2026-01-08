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
        Schema::create('my_vehicle_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('my_vehicle_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('my_vehicle_setting_id')
                ->constrained()
                ->on('my_vehicle_setting')
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
            $table->string('edit_vehicle_button_text')->nullable();
            $table->string('remove_vehicle_button_text')->nullable();
            $table->string('add_main_heading')->nullable();
            $table->string('edit_main_heading')->nullable();
            $table->string('mobile_indicate_field_label')->nullable();
            $table->string('make_label')->nullable();
            $table->string('make_placeholder')->nullable();
            $table->string('model_label')->nullable();
            $table->string('model_placeholder')->nullable();
            $table->string('license_plate_number_label')->nullable();
            $table->string('license_plate_number_placeholder')->nullable();
            $table->string('color_label')->nullable();
            $table->string('color_placeholder')->nullable();
            $table->string('year_label')->nullable();
            $table->string('year_placeholder')->nullable();
            $table->string('vehicle_type_label')->nullable();
            $table->string('vehicle_type_placeholder')->nullable();
            $table->string('fuel_label')->nullable();
            $table->string('electric_checkbox_label')->nullable();
            $table->string('hybrid_checkbox_label')->nullable();
            $table->string('gas_checkbox_label')->nullable();
            $table->string('set_primary_vehicle_label')->nullable();
            $table->string('yes_checkbox_label')->nullable();
            $table->string('no_checkbox_label')->nullable();
            $table->string('image_description_label')->nullable();
            $table->string('upload_profile_photo_image_placeholder')->nullable();
            $table->string('choose_file_image_placeholder')->nullable();
            $table->string('images_option_placeholder')->nullable();
            $table->string('add_vehicle_button_text')->nullable();
            $table->string('car_photo_label')->nullable();
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
        Schema::dropIfExists('my_vehicle_setting_detail');
        Schema::dropIfExists('my_vehicle_setting');
    }
};
