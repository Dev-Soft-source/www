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
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->string('female_user_message')->nullable();
            $table->string('star5_passenger_message')->nullable();
            $table->string('star4_passenger_message')->nullable();
            $table->string('star3_passenger_message')->nullable();
            $table->string('passenger_with_review_message')->nullable();
            $table->string('past_time_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['passenger_with_review_message', 'star3_passenger_message', 'star4_passenger_message',
                'star5_passenger_message', 'female_user_message', 'past_time_message'
            ]);
        });
    }
};
