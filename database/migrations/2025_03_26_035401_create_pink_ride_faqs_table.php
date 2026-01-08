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
        Schema::create('pink_ride_faqs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('pink_ride_faq_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pink_ride_faq_id')
                ->constrained()
                ->on('pink_ride_faqs')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('question')->nullable();
            $table->text('answer')->nullable();
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
        Schema::dropIfExists('pink_ride_faq_details');
        Schema::dropIfExists('pink_ride_faqs');
    }
};
