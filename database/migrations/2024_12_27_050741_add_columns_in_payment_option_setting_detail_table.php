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
            $table->text('card_name_error')->nullable();
            $table->text('card_number_error')->nullable();
            $table->text('card_type_error')->nullable();
            $table->text('month_error')->nullable();
            $table->text('year_error')->nullable();
            $table->text('cvv_error')->nullable();
            $table->text('address_error')->nullable();
            $table->text('city_error')->nullable();
            $table->text('province_error')->nullable();
            $table->text('postal_code_error')->nullable();
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
            $table->dropColumn(['postal_code_error', 'province_error', 'city_error', 'address_error', 'cvv_error',
                'year_error', 'month_error', 'card_type_error', 'card_number_error', 'card_name_error'
            ]);
        });
    }
};
