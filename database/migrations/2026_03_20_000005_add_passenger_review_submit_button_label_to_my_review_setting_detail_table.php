<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->string('passenger_review_submit_button_label')->nullable()->after('passenger_review_placeholder');
        });
    }

    public function down()
    {
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['passenger_review_submit_button_label']);
        });
    }
};

