<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->string('complete_address_passenger_message', 500)->nullable()->after('passenger_with_review_message');
        });
    }

    public function down()
    {
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->dropColumn('complete_address_passenger_message');
        });
    }
};

