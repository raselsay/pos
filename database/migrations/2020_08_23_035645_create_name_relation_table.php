<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNameRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('namerelations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('name_id');
            $table->string('rel_name');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->foreign('name_id')->references('id')->on('names')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('namerelations');
    }
}
