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
        Schema::create('features_setting', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->timestamps();
        });
        Schema::create('features_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('features_setting_id')
                ->constrained()
                ->on('features_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('name')->nullable();
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
        Schema::dropIfExists('features_setting_detail');
        Schema::dropIfExists('features_setting');
    }
};
