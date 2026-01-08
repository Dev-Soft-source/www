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
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->text('rides_taken_icon')->nullable();
            $table->text('passenger_driven_icon')->nullable();
            $table->text('km_shared_icon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edit_profile_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['km_shared_icon', 'passenger_driven_icon', 'rides_taken_icon']);
        });
    }
};
