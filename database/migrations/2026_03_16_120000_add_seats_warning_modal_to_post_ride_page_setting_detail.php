<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->string('seats_warning_modal_heading')->nullable()->after('price_warning_paragraph_2');
            $table->text('seats_warning_modal_paragraph')->nullable()->after('seats_warning_modal_heading');
            $table->string('seats_warning_modal_got_it_btn')->nullable()->after('seats_warning_modal_paragraph');
            $table->string('seats_warning_modal_learn_more_btn')->nullable()->after('seats_warning_modal_got_it_btn');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'seats_warning_modal_heading',
                'seats_warning_modal_paragraph',
                'seats_warning_modal_got_it_btn',
                'seats_warning_modal_learn_more_btn',
            ]);
        });
    }
};
