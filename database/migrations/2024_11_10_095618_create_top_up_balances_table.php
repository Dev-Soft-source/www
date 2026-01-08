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
        Schema::create('top_up_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->on('users')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade')->nullable();
                $table->foreignId('booking_id')
                ->constrained()
                ->on('bookings')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade')->nullable();
            $table->decimal('cr_amount',18,1)->nullable();
            $table->decimal('dr_amount',18,1)->nullable();
            $table->string('stripe_id')->nullable();
            $table->string('paypal_id')->nullable();
            $table->date('added_date')->nullable();
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
        Schema::dropIfExists('top_up_balances');
    }
};
