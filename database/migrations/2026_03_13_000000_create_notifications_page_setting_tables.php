<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Notifications page: green bar title and collapsible info paragraphs (ride, inbox, general update).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications_page_setting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('notifications_page_setting_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notifications_page_setting_id');
            $table->foreign('notifications_page_setting_id', 'notif_page_setting_detail_setting_id_fk')
                ->references('id')->on('notifications_page_setting')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('info_bar_title')->nullable()->comment('Stay connected – your chats live here');
            $table->text('info_paragraph_ride')->nullable()->comment('If the message is about a ride, tapping it will take you straight to that ride\'s details.');
            $table->text('info_paragraph_inbox')->nullable()->comment('If it\'s from another member (a driver or passenger), you\'ll be directed to the conversation in your Inbox.');
            $table->text('info_paragraph_general')->nullable()->comment('If it\'s a general update from ProximaRide, it will open right here for you to read.');
            $table->string('mark_all_as_read_button_label')->nullable()->comment('Mark all as read');
            $table->string('unread_label')->nullable()->comment('unread');
            $table->string('no_notifications_found_label')->nullable()->comment('No notifications found.');
            $table->string('caught_up_label')->nullable()->comment('You\'re all caught up!');
            $table->string('delete_button_label')->nullable()->comment('Delete');
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
        Schema::dropIfExists('notifications_page_setting_detail');
        Schema::dropIfExists('notifications_page_setting');
    }
};
