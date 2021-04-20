<?php

use App\Enums\DistributorStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('user_id');
            $table->json('name');
            $table->string('contact_person_name')->nullable();
            $table->string('name_en')->virtualAs('name->>"$.en"');
            $table->string('name_bn')->virtualAs('name->>"$.bn"');
            $table->string('email', 100)->index();
            $table->string('mobile', 20)->index();
            $table->double('lat')->nullable()->index();
            $table->double('long')->nullable()->index();
            $table->tinyInteger('status')->default(DistributorStatus::ACTIVE);
            $table->string('profile_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('address')->nullable();
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
        Schema::dropIfExists('distributors');
    }
}
