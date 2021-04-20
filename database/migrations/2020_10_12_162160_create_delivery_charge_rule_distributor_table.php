<?php

use App\Enums\DeliveryChargeRuleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryChargeRuleDistributorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_charge_rule_distributor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('delivery_charge_rule_id');
            $table->bigInteger('distributor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_charge_rule_distributor');
    }
}
