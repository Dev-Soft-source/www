<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('profile_photo_guidelines_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('profile_photo_guidelines_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_photo_guidelines_page_id', 'ppg_page_id_fk')
                ->constrained()
                ->on('profile_photo_guidelines_page_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id','ppg_lang_id_fk')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->text('meta_keywords', 1000)->nullable();
            $table->text('meta_description', 1000)->nullable();
            $table->text('main_heading')->nullable();
            $table->longText('main_text')->nullable();
            $table->text('example_label')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profile_photo_guidelines_page_setting_detail');
        Schema::dropIfExists('profile_photo_guidelines_page_setting');
    }
};
