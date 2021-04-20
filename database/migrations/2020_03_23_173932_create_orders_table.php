<?php

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id');
            $table->string('customer_mobile', 20);
            $table->bigInteger('distributor_id');
            $table->tinyInteger('status')->default(OrderStatus::CREATED);
            $table->tinyInteger('payment_status')->default(OrderPaymentStatus::UNPAID);
            $table->string('address', 250)->nullable();
            $table->double('sub_total')->default(0);
            $table->double('total')->default(0);
            $table->float('delivery_charge')->nullable();
            $table->text('remarks')->nullable();
            $table->json('misc');
            $table->string('tracking_id',8)->nullable()->index();
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
        Schema::dropIfExists('orders');
    }
}
