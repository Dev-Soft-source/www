<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('driver_page_setting_detail', function (Blueprint $table) {
            $table->string('no_reviews_label')->nullable()->after('reviews_heading');
        });
    }

    public function down()
    {
        Schema::table('driver_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('no_reviews_label');
        });
    }
};

