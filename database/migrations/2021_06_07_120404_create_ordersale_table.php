<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');          
            $table->string('dates',30);            
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('store_id')->nullable();
            $table->decimal('deb_qantity',16,2)->default(0);
            $table->decimal('cred_qantity',16,2)->default(0);
            $table->decimal('discount',16,2)->default(0);
            $table->decimal('price',16,2);
            $table->unsignedTinyInteger('action_id');
            $table->unsignedInteger('user_id')->default(0);
            $table->timestamps();
            $table->foreign('invoice_id')->references('id')->on('order_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_sales');
    }
}
