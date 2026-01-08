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
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->longText('agree_terms_label')->change();
            $table->longText('signin_label')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->string('agree_terms_label')->change();
            $table->string('signin_label')->change();
        });
    }
};
