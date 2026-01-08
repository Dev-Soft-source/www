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
        Schema::table('rides', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id'); // Adjust 'column_name' to place it after a specific column
        });

        // Adding random_id column to the 'transactions' table
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id'); // Adjust 'column_name' to place it after a specific column
        });

        Schema::table('bank_details', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id'); // Adjust 'column_name' to place it after a specific column
        });

        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id'); // Adjust 'column_name' to place it after a specific column
        });

        Schema::table('claim_rewards', function (Blueprint $table) {
            $table->string('random_id')->nullable()->after('id'); // Adjust 'column_name' to place it after a specific column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn('random_id');
        });

        // Dropping random_id column from the 'transactions' table
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('random_id');
        });

        // Dropping random_id column from the 'bank_details' table
        Schema::table('bank_details', function (Blueprint $table) {
            $table->dropColumn('random_id');
        });

        // Dropping random_string column from the 'phone_verifications' table
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->dropColumn('random_string');
        });

        // Dropping random_string column from the 'claim_rewards' table
        Schema::table('claim_rewards', function (Blueprint $table) {
            $table->dropColumn('random_string');
        });
    }
};
