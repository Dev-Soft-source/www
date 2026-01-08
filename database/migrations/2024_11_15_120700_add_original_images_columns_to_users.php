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
        Schema::table('users', function (Blueprint $table) {            
            $table->text('government_issued_original_id')->after('government_issued_id')->nullable();
            $table->text('student_card_original_upload')->after('student_card_upload')->nullable();
            $table->text('driver_license_original_upload')->after('driver_license_upload')->nullable();
            $table->text('profile_original_image')->after('profile_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
