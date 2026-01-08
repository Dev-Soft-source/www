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
        Schema::create('mobile_signup_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('mobile_signup_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_signup_setting_id')
                ->constrained()
                ->on('mobile_signup_setting')
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
            $table->string('or_label')->nullable();
            $table->string('first_name_label')->nullable();
            $table->string('first_name_placeholder')->nullable();
            $table->string('last_name_label')->nullable();
            $table->string('last_name_placeholder')->nullable();
            $table->string('email_label')->nullable();
            $table->string('email_placeholder')->nullable();
            $table->string('password_label')->nullable();
            $table->string('password_placeholder')->nullable();
            $table->string('confirm_password_label')->nullable();
            $table->string('confirm_password_placeholder')->nullable();
            $table->string('agree_terms_label')->nullable();
            $table->string('button_label')->nullable();
            $table->string('signin_label')->nullable();
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
        Schema::dropIfExists('mobile_signup_setting_detail');
        Schema::dropIfExists('mobile_signup_setting');
    }
};
