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
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn(['card_number', 'card_type', 'exp_month', 'exp_year', 'cvv_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('card_number')->after('name_on_card')->nullable();
            $table->string('card_type')->after('card_number')->nullable();
            $table->string('exp_month')->after('card_type');
            $table->string('exp_year')->after('exp_month');
            $table->string('cvv_code')->after('exp_year')->nullable();
        });
    }
};
