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
        Schema::table('bank_details', function (Blueprint $table) {
            $table->string('set_default')->after('status')->nullable();
            $table->string('paypal_email')->after('set_default')->nullable();
            $table->string('payment_status')->after('paypal_email')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_details', function (Blueprint $table) {
            $table->dropColumn(['payment_status']);
            $table->dropColumn(['set_default']);
            $table->dropColumn(['paypal_email']);
        });
    }
};
