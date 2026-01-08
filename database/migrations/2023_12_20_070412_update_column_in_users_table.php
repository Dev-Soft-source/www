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
            $table->longText('features')->nullable()->change();
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->longText('features')->change();
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
            $table->string('features')->default('')->change();
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->string('features')->change();
        });
    }
};
