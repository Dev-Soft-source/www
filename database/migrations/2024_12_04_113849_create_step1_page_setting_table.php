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
        Schema::create('step1_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('step1_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step1_page_setting_id')
                ->constrained()
                ->on('step1_page_setting')
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
            $table->string('required_label')->nullable();
            $table->string('first_name_label')->nullable();
            $table->string('last_name_label')->nullable();
            $table->string('gender_label')->nullable();
            $table->string('male_option_label')->nullable();
            $table->string('female_option_label')->nullable();
            $table->string('prefer_option_label')->nullable();
            $table->string('dob_label')->nullable();
            $table->string('country_label')->nullable();
            $table->string('state_label')->nullable();
            $table->string('city_label')->nullable();
            $table->string('zip_code_label')->nullable();
            $table->string('bio_label')->nullable();
            $table->string('button_label')->nullable();
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
        Schema::dropIfExists('step1_page_setting_detail');
        Schema::dropIfExists('step1_page_setting');
    }
};
