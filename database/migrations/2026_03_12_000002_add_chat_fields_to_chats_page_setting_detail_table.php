<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chats_page_setting_detail', function (Blueprint $table) {
            $table->text('driver_chat_with')->nullable();
            $table->text('empty_chat_placeholder')->nullable();
            $table->text('ride_detail_header')->nullable();
            $table->text('chat_start_mark')->nullable();
        });
    }

    public function down()
    {
        Schema::table('chats_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'driver_chat_with',
                'empty_chat_placeholder',
                'ride_detail_header',
                'chat_start_mark',
            ]);
        });
    }
};
