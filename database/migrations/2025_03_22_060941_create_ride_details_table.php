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
        Schema::create('ride_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')
                ->constrained()
                ->on('rides')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('departure')->nullable();
            $table->string('total_distance')->nullable();
            $table->string('total_duration')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('price', 18,2)->nullable();
            $table->time('time')->nullable();
            $table->date('date')->nullable();
            $table->time('destination_time')->nullable();
            $table->date('destination_date')->nullable();
            $table->time('completed_time')->nullable();
            $table->date('completed_date')->nullable();
            $table->boolean('default_ride')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ride_details');
    }
};
