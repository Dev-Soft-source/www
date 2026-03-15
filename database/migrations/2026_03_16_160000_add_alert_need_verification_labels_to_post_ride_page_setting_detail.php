<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->string('alert_need_government_photo_label')->nullable()->after('phone_required_modal_phone_btn');
            $table->string('alert_need_driver_license_label')->nullable()->after('alert_need_government_photo_label');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'alert_need_government_photo_label',
                'alert_need_driver_license_label',
            ]);
        });
    }
};
