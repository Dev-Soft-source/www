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
        Schema::create('referral_details', function (Blueprint $table) {
            $table->id();
            $table->integer('referral_user_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('type')->nullable();
            $table->decimal('booking_fee', 18,2)->nullable();
            $table->integer('driver_point')->nullable();
            $table->integer('student_point')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('referral_details');
    }
};
