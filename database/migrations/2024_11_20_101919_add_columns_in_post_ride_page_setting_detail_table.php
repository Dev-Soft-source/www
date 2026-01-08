<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('payment_methods_option1_tooltip')->after('payment_methods_option1')->nullable();
            $table->text('payment_methods_option2_tooltip')->after('payment_methods_option2')->nullable();
            $table->text('payment_methods_option3_tooltip')->after('payment_methods_option3')->nullable();
        });

        DB::table('post_ride_page_setting_detail')->update([
            'payment_methods_option1_tooltip' => 'Pay directly with cash at the time of service.',
            'payment_methods_option2_tooltip' => 'Complete the payment securely online.',
            'payment_methods_option3_tooltip' => "Cash payment with additional security measures.",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['payment_methods_option3_tooltip', 'payment_methods_option2_tooltip', 'payment_methods_option1_tooltip']);
        });
    }
};
