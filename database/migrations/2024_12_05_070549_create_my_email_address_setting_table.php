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
        Schema::create('my_email_address_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('my_email_address_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_address_setting_id')
                ->constrained()
                ->on('my_email_address_setting')
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
            $table->string('email_description_text')->nullable();
            $table->string('email_label')->nullable();
            $table->string('email_placeholder')->nullable();
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
        Schema::dropIfExists('my_email_address_setting_detail');
        Schema::dropIfExists('my_email_address_setting');
    }
};
