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
        Schema::table('close_my_account_setting_detail', function (Blueprint $table) {
            $table->string('why_closing_account_placeholder')->after('why_closing_account_label')->nullable();
            $table->string('improve_placeholder')->after('improve_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('close_my_account_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['improve_placeholder', 'why_closing_account_placeholder']);
        });
    }
};
