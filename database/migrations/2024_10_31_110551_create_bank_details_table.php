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
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')
                ->constrained()
                ->on('banks')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->nullable();
            $table->foreignId('user_id')
                ->constrained()
                ->on('users')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('bank_title')->nullable();
            $table->string('acc_no')->nullable();
            $table->string('iban')->nullable();
            $table->string('branch')->nullable();
            $table->string('address')->nullable();
            $table->decimal('admin_verify_amount', 18,2)->nullable();
            $table->decimal('user_verify_amount', 18,2)->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('bank_details');
    }
};
