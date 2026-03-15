<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->text('price_error_paragraph_1')->nullable()->after('agree_term_error');
            $table->text('price_error_paragraph_2')->nullable()->after('price_error_paragraph_1');
            $table->text('price_error_paragraph_3')->nullable()->after('price_error_paragraph_2');
        });
    }

    public function down()
    {
        Schema::table('post_ride_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['price_error_paragraph_1', 'price_error_paragraph_2', 'price_error_paragraph_3']);
        });
    }
};
