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
        Schema::create('chats_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('chats_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chats_page_setting_id')
                ->constrained()
                ->on('chats_page_setting')
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
            $table->text('old_messages_heading')->nullable();
            $table->text('no_messages_label')->nullable();
            $table->text('old_chat_page_main_heading')->nullable();
            $table->text('old_chat_page_no_messages_label')->nullable();
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
        Schema::dropIfExists('chats_page_setting_detail');
        Schema::dropIfExists('chats_page_setting');
    }
};
