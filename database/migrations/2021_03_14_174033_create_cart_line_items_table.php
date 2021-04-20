<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_line_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cart_id')->index();
            $table->bigInteger('product_id')->index();
            $table->integer('qty')->default(0);
            $table->double('unit_price');
            $table->double('discounted_price');
            $table->double('item_total');
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
        Schema::dropIfExists('cart_line_items');
    }
}
