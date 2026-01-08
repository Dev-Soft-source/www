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
        Schema::create('payment_option_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('payment_option_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_opt_setting_id')
                ->constrained()
                ->on('payment_option_setting')
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
            $table->string('mobile_default_card_tab')->nullable();
            $table->string('mobile_card_name_label')->nullable();
            $table->string('mobile_card_number_label')->nullable();
            $table->string('mobile_expiry_date_label')->nullable();
            $table->string('delete_card_button_text')->nullable();
            $table->string('add_new_card_button_text')->nullable();
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
        Schema::dropIfExists('payment_option_setting_detail');
        Schema::dropIfExists('payment_option_setting');
    }
};
