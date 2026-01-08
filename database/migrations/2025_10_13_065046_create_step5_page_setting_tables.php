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
        Schema::create('step5_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('step5_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step5_page_setting_id')
                ->constrained()
                ->on('step5_page_setting')
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
            $table->text('meta_keywords', 1000)->nullable();
            $table->text('meta_description', 1000)->nullable();
            $table->string('main_heading')->nullable();
            $table->string('main_label')->nullable();
            $table->string('sub_main_label')->nullable();
            $table->string('required_label')->nullable();
            $table->string('driver_license_label')->nullable();
            $table->string('photo_detail_label')->nullable();
            $table->string('mobile_photo_choose_file_label')->nullable();
            $table->string('skip_license')->nullable();
            $table->string('next_button_label')->nullable();
            $table->string('driver_license_error')->nullable();
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
        Schema::dropIfExists('step5_page_setting_detail');
        Schema::dropIfExists('step5_page_setting');
    }
};
