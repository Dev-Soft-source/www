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
        Schema::create('my_driver_license_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('my_driver_license_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_lic_setting_id')
                ->constrained()
                ->on('my_driver_license_setting')
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
            $table->string('mobile_indicate_required_field_label')->nullable();
            $table->string('driver_license_description_text')->nullable();
            $table->string('driver_license_label')->nullable();
            $table->string('web_upload_image_placeholder')->nullable();
            $table->string('mobile_driver_license_image_placeholder')->nullable();
            $table->string('mobile_choose_file_image_placeholder')->nullable();
            $table->string('mobile_image_type_placeholder')->nullable();
            $table->string('upload_button_text')->nullable();
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
        Schema::dropIfExists('my_driver_license_setting_detail');
        Schema::dropIfExists('my_driver_license_setting');
    }
};
