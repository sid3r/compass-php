<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('storehouses', function (Blueprint $table) {
        $table->id();
        $table->string('name', '50');
        $table->string('code', '20');
        $table->string('adress', '250');
        $table->string('status', '250')->default("active");
        $table->timestamps();
      });

      Schema::create('storehouse_users', function (Blueprint $table) {
        $table->id();
        $table->bigInteger('storehouse_id');
        $table->bigInteger('user_id');
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
      Schema::dropIfExists('storehouses');
    }
}
