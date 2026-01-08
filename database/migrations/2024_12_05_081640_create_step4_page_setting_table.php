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
        Schema::create('step4_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('step4_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step4_page_setting_id')
                ->constrained()
                ->on('step4_page_setting')
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
            $table->string('main_label')->nullable();
            $table->string('phone_label')->nullable();
            $table->string('skip_button_label')->nullable();
            $table->string('save_button_label')->nullable();
            $table->string('send_button_label')->nullable();
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
        Schema::dropIfExists('step4_page_setting_detail');
        Schema::dropIfExists('step4_page_setting');
    }
};
