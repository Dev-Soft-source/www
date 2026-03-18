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
            $table->string('reasons_required_error', 500)->nullable()->after('reason_label');
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
            $table->dropColumn('reasons_required_error');
        });
    }
};
