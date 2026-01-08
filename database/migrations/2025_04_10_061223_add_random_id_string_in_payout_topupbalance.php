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
        Schema::table('top_up_balances', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id');
        });

        Schema::table('coffee_wallets', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id');
        });
        
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top_up_balances', function (Blueprint $table) {
            $table->dropColumn('random_id');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn('random_id');
        });

        Schema::table('coffee_wallets', function (Blueprint $table) {
            $table->dropColumn('random_id');
        });
    }
};
