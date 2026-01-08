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
        Schema::create('coffee_wall_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('coffee_wall_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coffee_wall_setting_id')
                ->constrained()
                ->on('coffee_wall_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->text('name')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('main_heading')->nullable();
            $table->text('main_text')->nullable();
            $table->text('custom_amount_label')->nullable();
            $table->text('pay_button_label')->nullable();
            $table->text('frequency_label')->nullable();
            $table->text('email_label')->nullable();
            $table->text('name_label')->nullable();
            $table->text('phone_label')->nullable();
            $table->text('monthly_label')->nullable();
            $table->text('quarterly_label')->nullable();
            $table->text('semi_annually_label')->nullable();
            $table->text('annually_label')->nullable();
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
        Schema::dropIfExists('coffee_wall_setting_detail');
        Schema::dropIfExists('coffee_wall_setting');
    }
};
