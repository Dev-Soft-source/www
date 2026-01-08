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
        Schema::create('referral_page_setting_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_page_setting_id')
                ->constrained()
                ->on('referral_page_settings')
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
            $table->text('main_heading')->nullable();
            $table->text('your_referral_url_label')->nullable();
            $table->text('referral_description')->nullable();
            $table->text('my_referral_text')->nullable();
            $table->text('account_id_label')->nullable();
            $table->text('user_label')->nullable();
            $table->text('registered_on_label')->nullable();
            $table->text('no_referral_user_found_message')->nullable();
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
        Schema::dropIfExists('referral_page_setting_details');
    }
};
