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
        Schema::create('my_phone_no_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('my_phone_no_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phone_no_setting_id')
                ->constrained()
                ->on('my_phone_no_setting')
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
            $table->string('phone_no_description_text')->nullable();
            $table->string('unverified_number_label')->nullable();
            $table->string('mobile_verify_button_text')->nullable();
            $table->string('web_send_verification_code_button_text')->nullable();
            $table->string('delete_button_text')->nullable();
            $table->string('mobile_country_code_label')->nullable();
            $table->string('country_code_placeholder')->nullable();
            $table->string('mobile_phone_number_label')->nullable();
            $table->string('phone_number_placeholder')->nullable();
            $table->string('save_phoneno_button_text')->nullable();
            $table->string('send_verification_code_button_text')->nullable();
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
        Schema::dropIfExists('my_phone_no_setting_detail');
        Schema::dropIfExists('my_phone_no_setting');
    }
};
