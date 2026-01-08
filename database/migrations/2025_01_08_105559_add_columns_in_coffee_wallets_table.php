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
        Schema::table('coffee_wallets', function (Blueprint $table) {
            $table->string('subscription_id')->after('stripe_id')->nullable();
            $table->string('payment_method')->after('subscription_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coffee_wallets', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'subscription_id']);
        });
    }
};
