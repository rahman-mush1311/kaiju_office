<?php

use App\Enums\DeliveryChargeRuleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryChargeRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_charge_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->longText('description')->nullable();
            $table->decimal("min_basket_size");
            $table->decimal("max_basket_size");
            $table->decimal('delivery_charge');
            $table->tinyInteger('status')->default(DeliveryChargeRuleStatus::ACTIVE);
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
        Schema::dropIfExists('delivery_charge_rules');
    }
}
