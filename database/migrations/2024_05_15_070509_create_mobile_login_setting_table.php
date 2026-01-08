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
        Schema::create('mobile_login_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('mobile_login_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_login_setting_id')
                ->constrained()
                ->on('mobile_login_setting')
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
            $table->string('email_label')->nullable();
            $table->string('email_placeholder')->nullable();
            $table->string('password_label')->nullable();
            $table->string('password_placeholder')->nullable();
            $table->string('submit_button_label')->nullable();
            $table->string('forgot_password_label')->nullable();
            $table->string('or_label')->nullable();
            $table->string('signup_label')->nullable();
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
        Schema::dropIfExists('mobile_login_setting_detail');
        Schema::dropIfExists('mobile_login_setting');
    }
};
