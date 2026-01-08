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
        Schema::create('select_location_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('select_location_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_setting_id')
                ->constrained()
                ->on('select_location_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('select_origin_label')->nullable();
            $table->string('search_origin_label')->nullable();
            $table->string('no_origin_label')->nullable();
            $table->string('select_destination_label')->nullable();
            $table->string('search_destination_label')->nullable();
            $table->string('no_destination_label')->nullable();
            $table->string('select_country_label')->nullable();
            $table->string('search_country_label')->nullable();
            $table->string('no_country_label')->nullable();
            $table->string('select_state_label')->nullable();
            $table->string('search_state_label')->nullable();
            $table->string('no_state_label')->nullable();
            $table->string('select_city_label')->nullable();
            $table->string('search_city_label')->nullable();
            $table->string('no_city_label')->nullable();
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
        Schema::dropIfExists('select_location_setting_detail');
        Schema::dropIfExists('select_location_setting');
    }
};
