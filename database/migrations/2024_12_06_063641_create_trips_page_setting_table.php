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
        Schema::create('trips_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('trips_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trips_page_setting_id')
                ->constrained()
                ->on('trips_page_setting')
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
            $table->text('passenger_trips_heading')->nullable();
            $table->text('driver_rides_heading')->nullable();
            $table->text('upcoming_label')->nullable();
            $table->text('no_upcoming_trips_label')->nullable();
            $table->text('no_upcoming_rides_label')->nullable();
            $table->text('completed_label')->nullable();
            $table->text('no_completed_trips_label')->nullable();
            $table->text('no_completed_rides_label')->nullable();
            $table->text('cancelled_label')->nullable();
            $table->text('no_cancelled_trips_label')->nullable();
            $table->text('no_cancelled_rides_label')->nullable();
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
        Schema::dropIfExists('trips_page_setting_detail');
        Schema::dropIfExists('trips_page_setting');
    }
};
