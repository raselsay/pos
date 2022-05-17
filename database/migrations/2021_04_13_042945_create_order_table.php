<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',150);
            $table->string('business_name',150);
            $table->string('number',150);
            $table->string('email',150)->nullable();
            $table->string('adress',150);
            $table->string('current_adress',150)->nullable();
            $table->string('payment_method',150);
            $table->string('wallet_number',150);
            $table->string('transaction',150);
            $table->decimal('payment_ammount',20,2);
            $table->decimal('note',20,2)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
