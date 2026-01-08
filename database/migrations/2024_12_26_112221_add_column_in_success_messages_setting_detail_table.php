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
            $table->string('reviewed_driver_message')->nullable();
            $table->string('reviewed_passenger_message')->nullable();
            $table->string('reject_booking_message')->nullable();
            $table->string('cancel_booking_message')->nullable();
            $table->string('contact_form_message')->nullable();
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
            $table->dropColumn(['contact_form_message', 'cancel_booking_message', 'reject_booking_message',
                'reviewed_passenger_message', 'reviewed_driver_message'
            ]);
        });
    }
};
