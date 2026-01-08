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
        Schema::table('payment_option_setting_detail', function (Blueprint $table) {
            $table->string('no_payment_message')->nullable();
            $table->string('set_primary_card_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_option_setting_detail', function (Blueprint $table) {
            $table->dropColumn('no_payment_message');
            $table->dropColumn('set_primary_card_label');
        });
    }
};
