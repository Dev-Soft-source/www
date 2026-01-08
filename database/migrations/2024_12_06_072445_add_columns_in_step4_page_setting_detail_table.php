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
        Schema::table('step4_page_setting_detail', function (Blueprint $table) {
            $table->text('country_code_label')->nullable();
            $table->text('verify_button_label')->nullable();
            $table->text('verify_code_label')->nullable();
            $table->text('enter_code_label')->nullable();
            $table->text('request_code_label')->nullable();
            $table->text('second_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('step4_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['verify_button_label', 'country_code_label', 'verify_code_label', 'enter_code_label',
                'request_code_label', 'second_label'
            ]);
        });
    }
};
