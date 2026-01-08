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
            $table->string('slider_image')->after('slider_required_error')->nullable();
            $table->string('section1_pink_rides_image')->after('section1_pink_rides_label')->nullable();
            $table->text('section1_folks_rides_image')->after('section1_folks_rides_label')->nullable();
            $table->text('section1_customize_image')->after('section1_customize_label')->nullable();
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
            $table->dropColumn(['section1_customize_image', 'section1_folks_rides_image', 'section1_pink_rides_image', 'slider_image']);
        });
    }
};
