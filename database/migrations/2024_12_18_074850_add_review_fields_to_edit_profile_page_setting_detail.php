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

            $table->text('reply_heading_label')->nullable();
            $table->text('reply_placeholder')->nullable();
            $table->text('reply_submit_button_label')->nullable();
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
            $table->dropColumn([

                'reply_heading_label', 'reply_placeholder', 'reply_submit_button_label',
            ]);
        });
    }
};
