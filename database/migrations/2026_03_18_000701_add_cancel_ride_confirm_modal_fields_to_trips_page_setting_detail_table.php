<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->text('cancel_ride_confirm_modal_title')->nullable()->after('cancel_ride_confirm_ok_btn_text');
            $table->text('cancel_ride_confirm_modal_message')->nullable()->after('cancel_ride_confirm_modal_title');
            $table->text('cancel_ride_confirm_modal_no_btn_text')->nullable()->after('cancel_ride_confirm_modal_message');
            $table->text('cancel_ride_confirm_modal_yes_btn_text')->nullable()->after('cancel_ride_confirm_modal_no_btn_text');
        });
    }

    public function down()
    {
        Schema::table('trips_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'cancel_ride_confirm_modal_title',
                'cancel_ride_confirm_modal_message',
                'cancel_ride_confirm_modal_no_btn_text',
                'cancel_ride_confirm_modal_yes_btn_text',
            ]);
        });
    }
};

