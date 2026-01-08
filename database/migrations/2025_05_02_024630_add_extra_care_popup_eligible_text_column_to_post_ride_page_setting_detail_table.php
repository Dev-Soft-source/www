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
        Schema::table('post_ride_page_setting_sub_detail', function (Blueprint $table) {
            $table->text('extra_care_popup_eligible_text')->nullable();
            $table->text('feilds_required_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_ride_page_setting_sub_detail', function (Blueprint $table) {
            $table->dropColumn('extra_care_popup_eligible_text');
            $table->dropColumn('feilds_required_text');
        });
    }
};
