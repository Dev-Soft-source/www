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
        Schema::table('home_page_setting_detail', function (Blueprint $table) {
            $table->string('reasons_section_reliable_label')->after('reasons_section_use_description')->nullable();
            $table->text('reasons_section_reliable_description')->after('reasons_section_reliable_label')->nullable();
            $table->string('reasons_section_responsible_label')->after('reasons_section_reliable_description')->nullable();
            $table->text('reasons_section_responsible_description')->after('reasons_section_responsible_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('home_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('reasons_section_reliable_label');
            $table->dropColumn('reasons_section_reliable_description');
            $table->dropColumn('reasons_section_responsible_label');
            $table->dropColumn('reasons_section_responsible_description');
        });
    }
};
