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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->on('users')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('amount');
            $table->string('status')->default('0');
            $table->string('reason')->default('');
            $table->string('method');
            $table->string('account_no')->default('');
            $table->string('bank_name')->default('');
            $table->string('ifsc_code')->default('');
            $table->string('country')->default('');
            $table->string('paypal_email')->default('');
            $table->timestamp('on_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
