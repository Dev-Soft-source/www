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
        Schema::table('payout_option_setting_detail', function (Blueprint $table) {
            $table->string('interac_autodeposit_text_before')->nullable()->after('interac_autodeposit_tooltip');
            $table->string('interac_autodeposit_highlight')->nullable()->after('interac_autodeposit_text_before');
            $table->string('interac_autodeposit_text_after')->nullable()->after('interac_autodeposit_highlight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payout_option_setting_detail', function (Blueprint $table) {
            $table->dropColumn([
                'interac_autodeposit_text_before',
                'interac_autodeposit_highlight',
                'interac_autodeposit_text_after',
            ]);
        });
    }
};
