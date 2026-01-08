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
        Schema::create('reset_password_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('reset_password_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reset_pass_page_id')
                ->constrained()
                ->on('reset_password_page_setting')
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
        Schema::dropIfExists('reset_password_page_setting_detail');
        Schema::dropIfExists('reset_password_page_setting');
    }
};
