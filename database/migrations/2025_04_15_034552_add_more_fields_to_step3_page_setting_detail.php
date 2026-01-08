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
            $table->text('sub_heading')->nullable();
            $table->text('sub_main_label')->nullable();
            $table->text('liecense_section_heading')->nullable();
            $table->text('vehicle_section_heading')->nullable();
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
            $table->dropColumn(['sub_heading', 'sub_main_label', 'liecense_section_heading', 'vehicle_section_heading']);
        });
    }
};
