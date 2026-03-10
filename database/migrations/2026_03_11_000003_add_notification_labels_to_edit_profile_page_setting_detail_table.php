<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->string('notification_label')->nullable()->after('email_label');
            $table->string('email_notification_label')->nullable()->after('notification_label');
            $table->string('sms_notification_label')->nullable()->after('email_notification_label');
        });
    }

    public function down()
    {
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'notification_label',
                'email_notification_label',
                'sms_notification_label',
            ]);
        });
    }
};
