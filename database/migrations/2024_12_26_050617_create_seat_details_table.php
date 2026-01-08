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
        Schema::create('seat_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->nullable()
                ->constrained()
                ->on('rides')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('booking_id')->nullable()
                ->constrained()
                ->on('bookings')
                ->references('id')
                ->onDelete('set null')
                ->onUpdate('set null');
            $table->foreignId('user_id')->nullable()
                ->constrained()
                ->on('users')
                ->references('id')
                ->onDelete('set null')
                ->onUpdate('set null');
            $table->integer('seat_number')->nullable();
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('seat_details');
    }
};
