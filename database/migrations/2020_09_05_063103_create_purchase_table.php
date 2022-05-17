<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');    
            $table->string('dates',30);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('store_id')->nullable();;
            $table->decimal('deb_qantity',16,2)->default(0);
            $table->decimal('cred_qantity',16,2)->default(0);
            $table->decimal('price',16,2);
            $table->unsignedBigInteger('action_id')->default(0);
            $table->unsignedBigInteger('str_rel_id')->default(null);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('invoice_id')->references('id')->on('invpurchases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
