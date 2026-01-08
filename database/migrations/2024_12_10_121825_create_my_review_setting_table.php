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
        Schema::create('my_review_setting', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
        });
        Schema::create('my_review_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('my_review_setting_id')
                ->constrained()
                ->on('my_review_setting')
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
                $table->string('review_left_label')->nullable();
                $table->string('review_received_label')->nullable();
                $table->string('response_label')->nullable();
                $table->string('replied_label')->nullable();
                $table->string('reply_label')->nullable();
                $table->string('no_more_data_label')->nullable();
                $table->string('no_left_message')->nullable();
                $table->string('no_received_message')->nullable();
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
        Schema::dropIfExists('my_review_setting_detail');
        Schema::dropIfExists('my_review_setting');
    }
};
