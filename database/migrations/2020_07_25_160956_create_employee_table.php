<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100);
            $table->string('email',100)->unique()->nullable();
            $table->string('phone',20);
            $table->string('adress',100);
            $table->string('experience',100)->nullable();
            $table->string('nid',25)->nullable();
            $table->decimal('salary',16,2);
            $table->string('job_dept',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('photo',100)->nullable();
            $table->integer('users_id');
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
        Schema::dropIfExists('employees');
    }
}
