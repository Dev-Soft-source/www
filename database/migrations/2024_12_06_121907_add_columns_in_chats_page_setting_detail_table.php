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
            $table->text('notification_filter_what_label')->nullable();
            $table->text('notification_filter_search_filter_label')->nullable();
            $table->text('notification_filter_payment_label')->nullable();
            $table->text('notification_filter_all_label')->nullable();
            $table->text('notification_filter_booking_label')->nullable();
            $table->text('notification_filter_clear_btn_label')->nullable();
            $table->text('notification_filter_apply_btn_label')->nullable();
            $table->text('notification_filter_btn_label')->nullable();
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
            $table->dropColumn(['notification_filter_btn_label', 'notification_filter_apply_btn_label', 'notification_filter_clear_btn_label',
                'notification_filter_booking_label', 'notification_filter_all_label', 'notification_filter_payment_label', 'notification_filter_search_filter_label',
                'notification_filter_what_label'
            ]);
        });
    }
};
