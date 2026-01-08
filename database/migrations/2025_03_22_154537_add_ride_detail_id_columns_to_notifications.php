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
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('ride_detail_id')->nullable()
                ->constrained()
                ->on('ride_details')
                ->references('id')
                ->onUpdate('cascade');
            $table->string('departure')->nullable();
            $table->string('destination')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['ride_detail_id']);
            $table->dropColumn('ride_detail_id');
            $table->dropColumn('departure');
            $table->dropColumn('destination');
        });
    }
};
