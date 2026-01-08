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
        Schema::table('coffee_wallets', function (Blueprint $table) {
            $table->string('anonymous')->after('phone')->default('0');
            $table->string('designation')->after('anonymous')->default('All');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coffee_wallets', function (Blueprint $table) {
            $table->dropColumn(['designation', 'anonymous']);
        });
    }
};
