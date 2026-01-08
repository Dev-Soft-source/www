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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')
                ->constrained()
                ->on('rides')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('rating');
            $table->string('review');
            $table->timestamp('added_on')->useCurrent();
            $table->string('type')->default('1');
            $table->foreignId('posted_by')
                ->constrained()
                ->on('users')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('timeliness');
            $table->string('vehicle_condition');
            $table->string('safety');
            $table->string('conscious');
            $table->string('comfort');
            $table->string('communication');
            $table->string('attitude');
            $table->string('respect');
            $table->string('hygiene');
            $table->string('feature')->default('0');
            $table->string('recommend');
            $table->string('note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
};
