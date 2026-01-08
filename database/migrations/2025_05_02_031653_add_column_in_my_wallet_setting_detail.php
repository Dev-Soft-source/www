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
        Schema::table('my_wallet_setting_detail', function (Blueprint $table) {
            $table->text('purchase_top_up_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_wallet_setting_detail', function (Blueprint $table) {
            $table->dropColumn(['purchase_top_up_error']);
        });
    }
};
