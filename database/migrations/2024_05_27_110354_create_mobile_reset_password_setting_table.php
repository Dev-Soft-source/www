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
        Schema::create('mobile_reset_password_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('mobile_reset_password_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reset_page_id')
                ->constrained()
                ->on('mobile_reset_password_setting')
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
            $table->string('main_label')->nullable();
            $table->string('password_label')->nullable();
            $table->string('password_placeholder')->nullable();
            $table->string('confirm_password_label')->nullable();
            $table->string('confirm_password_placeholder')->nullable();
            $table->string('button_label')->nullable();
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
        Schema::dropIfExists('mobile_reset_password_setting_detail');
        Schema::dropIfExists('mobile_reset_password_setting');
    }
};
