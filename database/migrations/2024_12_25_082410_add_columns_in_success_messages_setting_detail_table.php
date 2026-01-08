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
            $table->text('select_reason')->nullable();
            $table->text('select_recommend')->nullable();
            $table->text('check_box')->nullable();
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
            $table->dropColumn(['check_box', 'select_recommend', 'select_reason']);
        });
    }
};
