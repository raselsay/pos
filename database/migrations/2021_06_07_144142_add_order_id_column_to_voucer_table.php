<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdColumnToVoucerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voucers', function (Blueprint $table) {
            $table->unsignedBigInteger("order_id")->nullable()->after('invoice_id');
            $table->foreign('order_id')->references('id')->on('order_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucers', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn("order_id");
        });
    }
}
