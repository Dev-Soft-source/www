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
        Schema::create('logout_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('logout_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logout_setting_id')
                ->constrained()
                ->on('logout_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('main_heading')->nullable();
            $table->string('confirmation_message_heading')->nullable();
            $table->string('confirmation_no_label')->nullable();
            $table->string('confirmation_yes_label')->nullable();
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
        Schema::dropIfExists('logout_setting_detail');
        Schema::dropIfExists('logout_setting');
    }
};
