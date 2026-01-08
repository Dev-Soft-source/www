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
            $table->dropColumn(['customer_id','card_id','brand','last4']);
            $table->string('name_on_card')->after('user_id')->nullable();
            $table->string('card_number')->after('name_on_card')->nullable();
            $table->string('card_type')->after('card_number')->nullable();
            $table->string('cvv_code')->after('exp_year')->nullable();
            $table->string('address')->after('cvv_code')->nullable();
            $table->string('primary_card')->after('address')->nullable();
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
            $table->string('customer_id');
            $table->string('card_id');
            $table->string('brand');
            $table->string('last4');
            $table->dropColumn(['name_on_card','card_number','card_type','cvv_code','address','primary_card']);
        });
    }
};
