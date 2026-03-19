<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->text('already_reveiwed_label')->nullable()->after('review_label');
        });
    }

    public function down()
    {
        Schema::table('my_review_setting_detail', function (Blueprint $table) {
            $table->dropColumn('already_reveiwed_label');
        });
    }
};
