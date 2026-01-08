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
        Schema::create('success_messages_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
        Schema::create('success_messages_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('messages_setting_id')
                ->constrained()
                ->on('success_messages_setting')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('language_id')
                ->constrained()
                ->on('languages')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('profile_update_message')->nullable();
            $table->string('vehicle_removed_message')->nullable();
            $table->string('vehicle_update_message')->nullable();
            $table->string('vehicle_add_message')->nullable();
            $table->string('password_update_message')->nullable();
            $table->string('phone_delete_message')->nullable();
            $table->string('phone_add_message')->nullable();
            $table->string('phone_verified_message')->nullable();
            $table->string('license_upload_message')->nullable();
            $table->string('student_card_upload_message')->nullable();
            $table->string('card_add_message')->nullable();
            $table->string('card_primary_message')->nullable();
            $table->string('card_delete_message')->nullable();
            $table->string('ride_cancel_message')->nullable();
            $table->string('bank_save_message')->nullable();
            $table->string('replied_message')->nullable();
            $table->string('ride_post_message')->nullable();
            $table->string('book_seat_message')->nullable();
            $table->string('email_verified_message')->nullable();
            $table->string('welcome_message')->nullable();
            $table->string('email_sent_message')->nullable();
            $table->string('hey_message')->nullable();
            $table->string('complete_profile_message')->nullable();
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
        Schema::dropIfExists('success_messages_setting_detail');
        Schema::dropIfExists('success_messages_setting');
    }
};
