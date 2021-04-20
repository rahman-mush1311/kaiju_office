<?php

use App\Enums\BrandStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('name');
            $table->string('name_en')->virtualAs('name->>"$.en"');
            $table->string('name_bn')->virtualAs('name->>"$.bn"');
            $table->string('slug')->index();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(BrandStatus::ACTIVE);
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
        Schema::dropIfExists('brands');
    }
}
