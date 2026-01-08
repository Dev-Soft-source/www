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
        Schema::create('user_message_count', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('contact_user_id')->nullable();
            $table->integer('user_inbox_count')->nullable();
            $table->integer('email_count')->nullable();
            $table->integer('sms_count')->nullable();
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
        Schema::dropIfExists('user_message_count');
    }
};
