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
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->text('make_placeholder')->nullable();
            $table->text('model_placeholder')->nullable();
            $table->text('mobile_driver_choose_file_label')->nullable();
            $table->text('photo_detail_label')->nullable();
            $table->text('mobile_photo_choose_file_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('step3_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['mobile_photo_choose_file_label', 'photo_detail_label', 'mobile_driver_choose_file_label',
                'model_placeholder', 'make_placeholder'
            ]);
        });
    }
};
