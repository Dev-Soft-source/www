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
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->string('reply_heading_label')->nullable();
            $table->string('reply_placeholder')->nullable();
            $table->string('reply_submit_button_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['reply_heading_label', 'reply_placeholder', 'reply_submit_button_label']);
        });
    }
};
