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

        Schema::create('post_ride_page_setting_sub_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_ride_page_id')
                ->constrained('post_ride_page_setting')  
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('extra_rides_trip_limit')->nullable();
            $table->text('city_not_fount_contact_text')->nullable();
          
           
         
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
        Schema::dropIfExists('post_ride_page_setting_sub_detail');
    }
};
