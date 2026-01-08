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
        Schema::table('my_vehicle_setting_detail', function (Blueprint $table) {
            $table->text('delete_photo_message')->nullable();
            $table->text('edit_photo_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_vehicle_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['delete_photo_message', 'edit_photo_label']);
        });
    }
};
