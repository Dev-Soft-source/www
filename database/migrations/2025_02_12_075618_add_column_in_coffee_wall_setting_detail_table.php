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
        Schema::table('coffee_wall_setting_detail', function (Blueprint $table) {
            $table->text('agree_terms_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coffee_wall_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['agree_terms_label']);
        });
    }
};
