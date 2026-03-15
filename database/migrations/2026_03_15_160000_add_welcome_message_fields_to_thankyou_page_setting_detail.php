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
            $table->string('welcome_page_title')->nullable()->after('top_up_message');
            $table->string('welcome_greeting')->nullable()->after('welcome_page_title');
            $table->text('welcome_paragraph_1')->nullable()->after('welcome_greeting');
            $table->text('welcome_paragraph_2')->nullable()->after('welcome_paragraph_1');
            $table->text('welcome_paragraph_3')->nullable()->after('welcome_paragraph_2');
            $table->text('welcome_paragraph_4')->nullable()->after('welcome_paragraph_3');
            $table->text('welcome_paragraph_5')->nullable()->after('welcome_paragraph_4');
            $table->string('welcome_complete_profile_btn')->nullable()->after('welcome_paragraph_5');
            $table->text('welcome_closing_line1')->nullable()->after('welcome_complete_profile_btn');
            $table->text('welcome_closing_line2')->nullable()->after('welcome_closing_line1');
            $table->text('welcome_closing_team_text')->nullable()->after('welcome_closing_line2');
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
                'welcome_page_title',
                'welcome_greeting',
                'welcome_paragraph_1',
                'welcome_paragraph_2',
                'welcome_paragraph_3',
                'welcome_paragraph_4',
                'welcome_paragraph_5',
                'welcome_complete_profile_btn',
                'welcome_closing_line1',
                'welcome_closing_line2',
                'welcome_closing_team_text',
            ]);
        });
    }
};
