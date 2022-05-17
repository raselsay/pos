<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('information', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name',150);
            $table->string('company_slogan',150);
            $table->string('adress',150);
            $table->string('phone',100);
            $table->string('email',100)->nullable();
            $table->string('country',150);
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('post_code',100)->nullable();
            $table->decimal('stock_warning',12,2)->nullable();
            $table->string('sms_api',200)->nullable();
            $table->string('sms_sender',200)->nullable();
            $table->boolean('sms_setting')->default(false);
            $table->string('logo')->nullable();
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
        Schema::dropIfExists('informations');
    }
}
