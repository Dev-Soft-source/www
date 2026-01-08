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
        Schema::table('chats_page_setting_detail', function (Blueprint $table) {
           $table->string('navigation_chat_label')->nullable();
           $table->string('navigation_my_trip_label')->nullable();
           $table->string('navigation_my_profile_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chats_page_setting_detail', function (Blueprint $table) {

            $table->dropColumn('navigation_chat_label');
            $table->dropColumn('navigation_my_trip_label');
            $table->dropColumn('navigation_my_profile_label');

        });
    }
};
