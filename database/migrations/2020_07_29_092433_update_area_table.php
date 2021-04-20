<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->bigInteger('ecom_area_id')->default(0);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->bigInteger('ecom_location_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn('ecom_area_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('ecom_location_id');
        });
    }
}
