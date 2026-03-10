<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('coffee_wall_setting_detail', function (Blueprint $table) {
            $table->text('contact_infomation_label')->nullable()->after('designation_option4');
            $table->text('faq_donors_label')->nullable()->after('contact_infomation_label');
            $table->text('faq_donors_1_question')->nullable()->after('faq_donors_label');
            $table->text('faq_donors_1_answer')->nullable()->after('faq_donors_1_question');
            $table->text('faq_donors_2_question')->nullable()->after('faq_donors_1_answer');
            $table->text('faq_donors_2_answer')->nullable()->after('faq_donors_2_question');
            $table->text('faq_donors_3_question')->nullable()->after('faq_donors_2_answer');
            $table->text('faq_donors_3_answer')->nullable()->after('faq_donors_3_question');
            $table->text('faq_donors_4_question')->nullable()->after('faq_donors_3_answer');
            $table->text('faq_donors_4_answer')->nullable()->after('faq_donors_4_question');
            $table->text('faq_beneficiary_label')->nullable()->after('faq_donors_4_answer');
            $table->text('faq_beneficiary_1_question')->nullable()->after('faq_beneficiary_label');
            $table->text('faq_beneficiary_1_answer')->nullable()->after('faq_beneficiary_1_question');
            $table->text('faq_beneficiary_2_question')->nullable()->after('faq_beneficiary_1_answer');
            $table->text('faq_beneficiary_2_answer')->nullable()->after('faq_beneficiary_2_question');
            $table->text('faq_beneficiary_3_question')->nullable()->after('faq_beneficiary_2_answer');
            $table->text('faq_beneficiary_3_answer')->nullable()->after('faq_beneficiary_3_question');
            $table->text('faq_beneficiary_4_question')->nullable()->after('faq_beneficiary_3_answer');
            $table->text('faq_beneficiary_4_answer')->nullable()->after('faq_beneficiary_4_question');
        });
    }

    public function down()
    {
        Schema::table('coffee_wall_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'contact_infomation_label',
                'faq_donors_label',
                'faq_donors_1_question',
                'faq_donors_1_answer',
                'faq_donors_2_question',
                'faq_donors_2_answer',
                'faq_donors_3_question',
                'faq_donors_3_answer',
                'faq_donors_4_question',
                'faq_donors_4_answer',
                'faq_beneficiary_label',
                'faq_beneficiary_1_question',
                'faq_beneficiary_1_answer',
                'faq_beneficiary_2_question',
                'faq_beneficiary_2_answer',
                'faq_beneficiary_3_question',
                'faq_beneficiary_3_answer',
                'faq_beneficiary_4_question',
                'faq_beneficiary_4_answer',
            ]);
        });
    }
};
