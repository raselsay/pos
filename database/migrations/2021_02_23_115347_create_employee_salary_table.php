<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('employee_id');
            $table->string('dates',10);
            $table->string('month',20);
            $table->decimal('balance',20,2)->nullable();
            $table->decimal('income_tax',20,2)->nullable();
            $table->decimal('medical',20,2)->nullable();
            $table->decimal('p_fund',20,2)->nullable();
            $table->decimal('basic',20,2);
            $table->decimal('bonus',20,2)->nullable();
            $table->decimal('over_time',20,2)->nullable();
            $table->decimal('payable',20,2);
            $table->unsignedInteger('user_id');
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
        Schema::dropIfExists('employee_salaries');
    }
}
