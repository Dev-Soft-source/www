<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->string('phone_required_modal_heading')->nullable()->after('seats_warning_modal_learn_more_btn');
            $table->text('phone_required_modal_body_before')->nullable()->after('phone_required_modal_heading');
            $table->string('phone_required_modal_link_text')->nullable()->after('phone_required_modal_body_before');
            $table->string('phone_required_modal_body_after')->nullable()->after('phone_required_modal_link_text');
            $table->string('phone_required_modal_close_btn')->nullable()->after('phone_required_modal_body_after');
            $table->string('phone_required_modal_phone_btn')->nullable()->after('phone_required_modal_close_btn');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'phone_required_modal_heading',
                'phone_required_modal_body_before',
                'phone_required_modal_link_text',
                'phone_required_modal_body_after',
                'phone_required_modal_close_btn',
                'phone_required_modal_phone_btn',
            ]);
        });
    }
};
