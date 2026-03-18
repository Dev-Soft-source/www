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
        Schema::table('media_setting_detail', function (Blueprint $table) {
            $table->string('agency_label')->nullable()->after('read_article_button_label');
            $table->string('added_by_label')->nullable()->after('agency_label');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['agency_label', 'added_by_label']);
        });
    }
};

