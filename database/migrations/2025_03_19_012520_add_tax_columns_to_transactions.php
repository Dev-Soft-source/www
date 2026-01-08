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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('tax_amount', 18,2)->default(0);
            $table->decimal('tax_percentage', 18,2)->default(0);
            $table->string('tax_type')->nullable();
            $table->string('deduct_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['tax_amount']);
            $table->dropColumn(['tax_percentage']);
            $table->dropColumn(['tax_type']);
            $table->dropColumn(['deduct_type']);
        });
    }
};
