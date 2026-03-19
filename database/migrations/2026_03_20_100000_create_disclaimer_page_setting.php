<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disclaimer_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('disclaimer_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disclaimer_page_id')
                ->constrained()
                ->on('disclaimer_page_setting')
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
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('main_heading')->nullable();
            $table->longText('main_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disclaimer_page_setting_detail');
        Schema::dropIfExists('disclaimer_page_setting');
    }
};
