<?php

use App\Enums\DistributorProductStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributor_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('distributor_id')->index();
            $table->bigInteger('product_id')->index();
            $table->tinyInteger('status')->default(DistributorProductStatus::AVAILABLE);
            $table->double('distributor_price');
            $table->integer('min_order_qty')->default(0);
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
        Schema::dropIfExists('distributor_products');
    }
}
