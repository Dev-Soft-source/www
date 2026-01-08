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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->on('users')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('ride_id')
                ->constrained()
                ->on('rides')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('seats');
            $table->timestamp('booked_on')->useCurrent();
            $table->string('status')->default('0');
            $table->string('free_ride')->default('0');
            $table->string('time_limit')->default('0');
            $table->string('booking_credit')->default('0');
            $table->string('reminder')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
