<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('step5_page_setting_detail', function (Blueprint $table) {
            $table->text('whatsapp_validation_title')->nullable()->after('logout_button_label');
            $table->text('whatsapp_validation_message')->nullable()->after('whatsapp_validation_title');
            $table->text('whatsapp_not_available_title')->nullable()->after('whatsapp_validation_message');
            $table->text('whatsapp_not_available_message')->nullable()->after('whatsapp_not_available_title');
            $table->text('whatsapp_success_title')->nullable()->after('whatsapp_not_available_message');
            $table->text('whatsapp_success_message')->nullable()->after('whatsapp_success_title');
            $table->text('whatsapp_error_title')->nullable()->after('whatsapp_success_message');
            $table->text('whatsapp_error_message')->nullable()->after('whatsapp_error_title');
            $table->text('whatsapp_limit_title')->nullable()->after('whatsapp_error_message');
            $table->text('whatsapp_limit_message')->nullable()->after('whatsapp_limit_title');
            $table->text('whatsapp_default_title')->nullable()->after('whatsapp_limit_message');
            $table->text('whatsapp_default_message')->nullable()->after('whatsapp_default_title');
        });
    }

    public function down()
    {
        Schema::table('step5_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_validation_title',
                'whatsapp_validation_message',
                'whatsapp_not_available_title',
                'whatsapp_not_available_message',
                'whatsapp_success_title',
                'whatsapp_success_message',
                'whatsapp_error_title',
                'whatsapp_error_message',
                'whatsapp_limit_title',
                'whatsapp_limit_message',
                'whatsapp_default_title',
                'whatsapp_default_message',
            ]);
        });
    }
};
