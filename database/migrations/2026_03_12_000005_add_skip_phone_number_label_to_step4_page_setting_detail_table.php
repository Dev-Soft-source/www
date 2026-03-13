<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('step5_page_setting_detail', function (Blueprint $table) {
            $table->text('skip_phone_number_label')->nullable()->after('skip_button_label');
        });
    }

    public function down()
    {
        Schema::table('step5_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('skip_phone_number_label');
        });
    }
};
