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
        Schema::create('close_my_account_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('close_my_account_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('close_acc_setting_id')
                ->constrained()
                ->on('close_my_account_setting')
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
            $table->string('warning_text')->nullable();
            $table->string('mobile_indicate_required_field_label')->nullable();
            $table->string('closing_account_label')->nullable();
            $table->string('apply_reason_label')->nullable();
            $table->string('reason_label')->nullable();
            $table->string('not_say_checkbox_label')->nullable();
            $table->string('customer_service_checkbox_label')->nullable();
            $table->string('technical_issue_checkbox_label')->nullable();
            $table->string('dont_use_checkbox_label')->nullable();
            $table->string('another_account_checkbox_label')->nullable();
            $table->string('did_not_get_booking_checkbox_label')->nullable();
            $table->string('did_not_find_ride_checkbox_label')->nullable();
            $table->string('did_not_find_destination_checkbox_label')->nullable();
            $table->string('other_checkbox_label')->nullable();
            $table->string('recommend_heading')->nullable();
            $table->string('yes_checkbox_label')->nullable();
            $table->string('no_checkbox_label')->nullable();
            $table->string('prefer_not_checkbox_label')->nullable();
            $table->string('why_closing_account_label')->nullable();
            $table->string('improve_label')->nullable();
            $table->string('close_my_account_checkbox')->nullable();
            $table->string('close_account_button_text')->nullable();
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
        Schema::dropIfExists('close_my_account_setting_detail');
        Schema::dropIfExists('close_my_account_setting');
    }
};
