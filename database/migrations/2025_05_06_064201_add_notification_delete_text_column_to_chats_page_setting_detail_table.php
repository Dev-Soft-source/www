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
            $table->text('notification_delete_text')->nullable();
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
            $table->dropColumn('notification_delete_text');
        });
    }
};
