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
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('features_option14')->nullable()->after('features_option13');
            $table->text('features_option15')->nullable()->after('features_option14');
            $table->text('features_option16')->nullable()->after('features_option15');
            $table->text('features_option17')->nullable()->after('features_option16');
            $table->longText('pink_ride_disclaimers_description')->nullable()->after('disclaimers_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['features_option14','features_option15','features_option16','pink_ride_disclaimers_description']);
        });
    }
};
