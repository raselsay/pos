<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('bank_id');
            $table->string('dates',16);
            $table->string('nickname')->nullable();
            $table->unsignedBigInteger('category')->nullable();
            $table->unsignedInteger('data_id');
            $table->string('transaction')->unique()->nullable();
            $table->decimal('debit',16,2)->default(false);
            $table->decimal('credit',16,2)->default(false);
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->tinyInteger('pay_action_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('voucers');
    }
}
