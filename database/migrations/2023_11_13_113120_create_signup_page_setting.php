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
        Schema::create('signup_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('signup_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signup_page_setting_id')
                ->constrained()
                ->on('signup_page_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->text('meta_keywords', 1000)->nullable();
            $table->text('meta_description', 1000)->nullable();
            $table->string('main_heading')->nullable();
            $table->string('social_media_profile_label')->nullable();
            $table->string('or_label')->nullable();
            $table->string('required_label')->nullable();
            $table->string('first_name_placeholder')->nullable();
            $table->string('last_name_placeholder')->nullable();
            $table->string('email_placeholder')->nullable();
            $table->string('password_placeholder')->nullable();
            $table->string('confirm_password_placeholder')->nullable();
            $table->string('agree_terms_label')->nullable();
            $table->string('button_label')->nullable();
            $table->string('after_button_label')->nullable();
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
        Schema::dropIfExists('signup_page_setting_detail');
        Schema::dropIfExists('signup_page_setting');
    }
};
