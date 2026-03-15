<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('error_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('error_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('error_page_setting_id')
                ->constrained('error_page_setting')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('error_404_heading')->nullable();
            $table->text('error_404_paragraph_1')->nullable();
            $table->text('error_404_paragraph_2')->nullable();
            $table->string('error_404_back_home_btn')->nullable();
            $table->string('error_404_contact_btn')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('error_page_setting_detail');
        Schema::dropIfExists('error_page_setting');
    }
};
