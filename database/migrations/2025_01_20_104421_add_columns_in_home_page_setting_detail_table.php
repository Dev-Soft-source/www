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
        Schema::table('home_page_setting_detail', function (Blueprint $table) {
            $table->text('movement_section_icon')->after('movement_section_heading')->nullable();
            $table->text('news_section_icon1')->after('news_section_heading')->nullable();
            $table->text('news_section_icon2')->after('news_section_icon1')->nullable();
            $table->text('news_section_icon3')->after('news_section_icon2')->nullable();
            $table->text('news_section_icon4')->after('news_section_icon3')->nullable();
            $table->text('use_section_point1_image')->after('use_section_point1_label')->nullable();
            $table->text('use_section_point2_image')->after('use_section_point2_label')->nullable();
            $table->text('use_section_point3_image')->after('use_section_point3_label')->nullable();
            $table->text('use_section_point4_image')->after('use_section_point4_label')->nullable();
            $table->text('payment_section_icon1')->after('payment_section_text')->nullable();
            $table->text('payment_section_icon2')->after('payment_section_icon1')->nullable();
            $table->text('payment_section_icon3')->after('payment_section_icon2')->nullable();
            $table->text('payment_section_icon4')->after('payment_section_icon3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('home_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['movement_section_icon', 'news_section_icon1', 'news_section_icon2', 'news_section_icon3',
                'news_section_icon4', 'use_section_point1_image', 'use_section_point2_image', 'use_section_point3_image',
                'use_section_point4_image', 'payment_section_icon1', 'payment_section_icon2', 'payment_section_icon3',
                'payment_section_icon4'
            ]);
        });
    }
};
