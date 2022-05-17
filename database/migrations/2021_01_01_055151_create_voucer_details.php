<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucer_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('voucer_id');
            $table->string('details',200);
            $table->decimal('qantity',20,2);
            $table->decimal('ammount',20,2);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('voucer_id')->references('id')->on('voucers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucerdetails');
    }
}
