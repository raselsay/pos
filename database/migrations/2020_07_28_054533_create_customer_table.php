<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name',100)->nullable();
            $table->string('name',100);
            $table->decimal('opening_balance',16,2)->default(0);
            $table->decimal('maximum_due',16,2)->default(500);
            $table->string('phone1',25)->unique();
            $table->string('phone2',25)->nullable()->unique();
            $table->string('email',100)->nullable()->unique();
            $table->string('birth_date',100)->nullable();
            $table->string('marriage_date',100)->nullable();
            $table->string('adress',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('postal_code',50)->nullable();
            $table->integer('stutus')->default(1);
            $table->string('group_types',50)->nullable();
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
        Schema::dropIfExists('customers');
    }
}
