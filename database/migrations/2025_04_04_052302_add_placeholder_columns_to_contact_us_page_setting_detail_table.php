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
        Schema::table('contact_us_page_setting_detail', function (Blueprint $table) {
            $table->text('placeholder_name')->nullable();
            $table->text('placeholder_email')->nullable();
            $table->text('placeholder_phone')->nullable();
            $table->text('placeholder_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_us_page_setting_detail', function (Blueprint $table) {
            //
        });
    }
};
