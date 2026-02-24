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
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('step1')->default(0);
            $table->tinyInteger('step2')->default(0);
            $table->tinyInteger('step3')->default(0);
            $table->tinyInteger('step4')->default(0);
            $table->tinyInteger('step5')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['step1', 'step2', 'step3', 'step4', 'step5']);
        });
    }
};
