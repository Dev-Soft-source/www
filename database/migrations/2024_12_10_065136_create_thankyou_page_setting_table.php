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
        Schema::create('thankyou_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('thankyou_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thankyou_page_setting_id')
                ->constrained()
                ->on('thankyou_page_setting')
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
            $table->text('forget_close_btn_label')->nullable();
            $table->text('forget_password_message')->nullable();
            $table->text('rest_password_btn_label')->nullable();
            $table->text('good_bye_btn_label')->nullable();
            $table->text('close_account_message')->nullable();
            $table->text('account_close_heading')->nullable();
            $table->text('login_btn_label')->nullable();
            $table->text('done_btn_label')->nullable();
            $table->text('instant_booking_message')->nullable();
            $table->text('manual_booking_message')->nullable();
            $table->text('top_up_message')->nullable();
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
        Schema::dropIfExists('thankyou_page_setting_detail');
        Schema::dropIfExists('thankyou_page_setting');
    }
};
