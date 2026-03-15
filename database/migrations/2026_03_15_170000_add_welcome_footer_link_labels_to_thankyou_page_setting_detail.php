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
        Schema::table('thankyou_page_setting_detail', function (Blueprint $table) {
            $table->string('welcome_footer_help_contact')->nullable()->after('welcome_closing_team_text');
            $table->string('welcome_footer_terms_use')->nullable()->after('welcome_footer_help_contact');
            $table->string('welcome_footer_coffee_on_wall')->nullable()->after('welcome_footer_terms_use');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thankyou_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'welcome_footer_help_contact',
                'welcome_footer_terms_use',
                'welcome_footer_coffee_on_wall',
            ]);
        });
    }
};
