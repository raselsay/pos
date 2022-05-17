<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderinvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dates', 30);
            $table->string('issue_dates', 30)->nullable();
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('transport_id')->nullable();
            $table->integer('total_item');
            $table->decimal('discount', 16, 2)->nullable();
            $table->decimal('vat', 16, 2)->nullable();
            $table->decimal('fine', 16, 2)->nullable();
            $table->decimal('labour_cost', 16, 2)->nullable();
            $table->decimal('transport', 16, 2)->nullable();
            $table->decimal('total_payable', 16, 2);
            $table->decimal('total', 16, 2);
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedTinyInteger('action_id')->default(0);
            $table->unsignedInteger('user_id');
            $table->text('note',500)->nullable();
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
        Schema::dropIfExists('order_invoices');
    }
}
