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
            $table->text('confirm_cancel_noshow')->nullable();
            $table->text('cancel_noshow_take_me_back')->nullable();
            $table->text('cancel_noshow_are_you_sure')->nullable();
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
            $table->dropColumn('confirm_cancel_noshow');
            $table->dropColumn('cancel_noshow_take_me_back');
            $table->dropColumn('cancel_noshow_are_you_sure');
        });
    }
};
