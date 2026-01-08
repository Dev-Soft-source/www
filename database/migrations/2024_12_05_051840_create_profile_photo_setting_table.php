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
        Schema::create('profile_photo_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('profile_photo_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_photo_setting_id')
                ->constrained()
                ->on('profile_photo_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->string('main_heading')->nullable();
            $table->string('mobile_upload_photo_tooltip')->nullable();
            $table->string('mobile_upload_new_image_button_text')->nullable();
            $table->string('save_button_text')->nullable();
            $table->string('upload_profile_photo_placeholder')->nullable();
            $table->string('choose_file_placeholder')->nullable();
            $table->string('images_option_placeholder')->nullable();
            $table->string('mobile_indicate_required_field_label')->nullable();
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
        Schema::dropIfExists('profile_photo_setting_detail');
        Schema::dropIfExists('profile_photo_setting');
    }
};
