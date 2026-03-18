<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->string('star5_passenger_with_complete_address_message', 500)->nullable()->after('complete_address_passenger_message');
        });
    }

    public function down()
    {
        Schema::table('success_messages_setting_detail', function (Blueprint $table) {
            $table->dropColumn('star5_passenger_with_complete_address_message');
        });
    }
};

