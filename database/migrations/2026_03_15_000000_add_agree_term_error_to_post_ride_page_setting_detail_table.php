<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('agree_term_error')->nullable()->after('agree_terms_label');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('agree_term_error');
        });
    }
};
