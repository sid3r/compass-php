<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tel')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('region')->nullable();
            $table->string('activity')->nullable();
            $table->string('nif')->nullable();
            $table->string('rc')->nullable();
            $table->string('ai')->nullable();
            $table->timestamps();
        });

        Schema::create('company_tags', function (Blueprint $table) {
          $table->id();
          $table->bigInteger('company_id');
          $table->bigInteger('tag_id');
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
        Schema::dropIfExists('entreprises');
    }
}
