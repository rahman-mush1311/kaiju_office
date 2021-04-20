<?php

use App\Enums\ProductStatus;
use App\Enums\VisibilityStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->string('name_en')->virtualAs('name->>"$.en"');
            $table->string('name_bn')->virtualAs('name->>"$.bn"');
            $table->string('slug')->index();
            $table->double('mrp');
            $table->double('trade_price');
            $table->string('short_description', 200)->nullable();
            $table->text('long_description')->nullable();
            $table->tinyInteger('status')->default(ProductStatus::INACTIVE);
            $table->string('image')->nullable();
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
        Schema::dropIfExists('products');
    }
}
