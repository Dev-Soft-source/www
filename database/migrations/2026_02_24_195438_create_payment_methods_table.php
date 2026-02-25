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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway'); // stripe / paypal
            $table->string('payment_method_id'); // pm_xxxxx
            $table->string('type'); // card, paypal, apple_pay, google_pay
            $table->string('brand')->nullable(); // Visa, Mastercard
            $table->string('last4')->nullable();
            $table->string('email')->nullable(); // for PayPal
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('payment_methods');
    }
};
