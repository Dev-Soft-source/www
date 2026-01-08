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
            $table->string('no_user_found_message')->nullable();
            $table->string('no_user_match_message')->nullable();
            $table->string('image_size_error_message')->nullable();
            $table->string('incorrect_password_message')->nullable();
            $table->string('incorrect_code_message')->nullable();
            $table->string('already_added_card_message')->nullable();
            $table->string('ride_schedule_message')->nullable();
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
            $table->dropColumn(['no_user_found_message', 'no_user_match_message', 'image_size_error_message', 'incorrect_password_message',
                'incorrect_code_message', 'already_added_card_message', 'ride_schedule_message'
            ]);
        });
    }
};
