<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->text('no_reviews_label')->nullable()->after('review_label');
        });
    }

    public function down()
    {
        Schema::table('ride_detail_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('no_reviews_label');
        });
    }
};
