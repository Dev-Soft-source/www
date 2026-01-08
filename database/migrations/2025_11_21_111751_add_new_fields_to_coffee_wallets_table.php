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
            $table->boolean('notify_coffee_used')->default(false)->after('designation');
            $table->boolean('donation_acknowledgment')->default(false)->after('notify_coffee_used');
            $table->boolean('terms_privacy')->default(false)->after('donation_acknowledgment');
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
            $table->dropColumn(['notify_coffee_used', 'donation_acknowledgment', 'terms_privacy']);
        });
    }
};
