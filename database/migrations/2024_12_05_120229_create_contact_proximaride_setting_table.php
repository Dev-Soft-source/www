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
        Schema::create('contact_proximaride_setting', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
        });
        Schema::create('contact_proximaride_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_pr_setting_id')
                ->constrained()
                ->on('contact_proximaride_setting')
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
            $table->string('mobile_indicate_required_field_label')->nullable();
            $table->string('your_full_name_label')->nullable();
            $table->string('your_full_name_placeholder')->nullable();
            $table->string('your_phone_label')->nullable();
            $table->string('your_phone_placeholder')->nullable();
            $table->string('your_email_address_label')->nullable();
            $table->string('your_email_address_placeholder')->nullable();
            $table->string('your_message_label')->nullable();
            $table->string('submit_button_text')->nullable();
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
        Schema::dropIfExists('contact_proximaride_setting_detail');
        Schema::dropIfExists('contact_proximaride_setting');
    }
};
