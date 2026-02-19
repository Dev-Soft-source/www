<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_text_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slug_id');
            $table->unsignedBigInteger('language_id');
            $table->text('name')->nullable();
            $table->text('icon')->nullable();
            $table->timestamps();

            $table->foreign('slug_id')->references('id')->on('site_texts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(['slug_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_text_detail');
    }
};
