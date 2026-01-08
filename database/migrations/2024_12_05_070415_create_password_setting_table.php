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
        Schema::create('password_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('password_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('password_setting_id')
                ->constrained()
                ->on('password_setting')
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
            $table->string('password_description_text')->nullable();
            $table->string('mobile_indicate_required_field_label')->nullable();
            $table->string('current_password_label')->nullable();
            $table->string('current_password_placeholder')->nullable();
            $table->string('new_password_label')->nullable();
            $table->string('new_password_placeholder')->nullable();
            $table->string('confirm_new_password_label')->nullable();
            $table->string('confirm_new_password_placeholder')->nullable();
            $table->string('update_button_text')->nullable();
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
        Schema::dropIfExists('password_setting_detail');
        Schema::dropIfExists('password_setting');
    }
};
