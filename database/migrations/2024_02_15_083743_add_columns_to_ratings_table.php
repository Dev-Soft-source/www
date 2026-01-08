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
        Schema::table('ratings', function (Blueprint $table) {
            $table->foreignId('posted_to')->nullable()->after('type')
                ->constrained()
                ->on('bookings')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('vehicle_condition')->nullable()->change();
            $table->string('rating')->nullable()->change();
            $table->string('timeliness')->nullable()->change();
            $table->string('safety')->nullable()->change();
            $table->string('conscious')->nullable()->change();
            $table->string('comfort')->nullable()->change();
            $table->string('communication')->nullable()->change();
            $table->string('attitude')->nullable()->change();
            $table->string('respect')->nullable()->change();
            $table->string('hygiene')->nullable()->change();
            $table->string('recommend')->nullable()->change();
            $table->string('note')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['posted_to']);
        });
    }
};
