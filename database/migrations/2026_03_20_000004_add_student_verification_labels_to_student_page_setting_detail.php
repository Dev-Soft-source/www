<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_page_setting_detail', function (Blueprint $table) {
            $table->text('success_upload_card_label')->nullable()->after('page_description');
            $table->text('success_upload_card_title')->nullable()->after('success_upload_card_label');
            $table->text('different_copy_label')->nullable()->after('success_upload_card_title');
            $table->text('expire_student_card_label')->nullable()->after('different_copy_label');
        });
    }

    public function down()
    {
        Schema::table('student_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'success_upload_card_label',
                'success_upload_card_title',
                'different_copy_label',
                'expire_student_card_label',
            ]);
        });
    }
};
