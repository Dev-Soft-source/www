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
        Schema::create('media_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('media_setting_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_setting_id')
                ->constrained()
                ->on('media_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Multi-language fields for media/news page
            $table->string('name')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('main_heading')->nullable();
            $table->string('read_article_button_label')->nullable();

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
        Schema::dropIfExists('media_setting_detail');
        Schema::dropIfExists('media_setting');
    }
};

