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
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->string('app_main_heading')->nullable();
            $table->string('app_agree_terms_part1_label')->nullable();
            $table->string('app_agree_terms_link1_label')->nullable();
            $table->string('app_agree_terms_link2_label')->nullable();
            $table->string('app_agree_terms_part2_label')->nullable();
            $table->string('app_agree_terms_link3_label')->nullable();
            $table->string('app_agree_terms_part3_label')->nullable();
            $table->string('no_account_label')->nullable();
            $table->string('signin_link_label')->nullable();
            $table->string('now_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signup_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['now_label', 'signin_link_label', 'no_account_label', 'app_agree_terms_part3_label', 'app_agree_terms_link3_label',
                'app_agree_terms_part2_label', 'app_agree_terms_link2_label', 'app_agree_terms_link1_label', 'app_agree_terms_part1_label', 'app_main_heading'
            ]);
        });
    }
};
