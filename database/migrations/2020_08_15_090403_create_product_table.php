<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name',150);
            $table->integer('category');
            $table->integer('child_category');
            $table->string('product_code',100)->nullable()->unique();
            $table->string('model_no',100)->nullable();
            $table->string('warranty',100)->nullable();
            $table->string('product_type',100)->nullable();
            $table->string('packaging',100)->nullable();
            $table->decimal('buy_price',16,2);
            $table->decimal('sale_price',16,2);
            $table->string('photo',100)->nullable();
            $table->integer('user_id');
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
        Schema::dropIfExists('products');
    }
}
