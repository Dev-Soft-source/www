<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->text('booking_disclaimer_firm_tooltip')->nullable()->after('booking_disclaimer_firm');
            $table->text('fee_student_pending_text')->nullable()->after('booking_disclaimer_firm_tooltip');
            $table->text('fee_charge_waived')->nullable()->after('fee_student_pending_text');
            $table->text('coffee_wall_cash_text')->nullable()->after('fee_charge_waived');
            $table->text('coffee_wall_online_payment_text')->nullable()->after('coffee_wall_cash_text');
            $table->text('coffee_wall_secure_cash_text')->nullable()->after('coffee_wall_online_payment_text');
            $table->text('note_for_students_text')->nullable()->after('coffee_wall_secure_cash_text');
            $table->text('seats_available')->nullable()->after('note_for_students_text');
        });
    }

    public function down()
    {
        Schema::table('booking_page_setting_detail', function (Blueprint $table) {
            $table->dropColumn('booking_disclaimer_firm_tooltip');
            $table->dropColumn('fee_student_pending_text');
            $table->dropColumn('fee_charge_waived');
            $table->dropColumn('coffee_wall_cash_text');
            $table->dropColumn('coffee_wall_online_payment_text');
            $table->dropColumn('coffee_wall_secure_cash_text');
            $table->dropColumn('note_for_students_text');
            $table->dropColumn('seats_available');
        });
    }
};
