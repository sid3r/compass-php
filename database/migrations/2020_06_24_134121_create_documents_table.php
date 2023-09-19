<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{   
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('type', '25');
            $table->string('status', '25');
            $table->bigInteger('code')->nullable();
            $table->bigInteger('company_id')->nullable();
            $table->string('date')->nullable();
            $table->integer('vatrate')->nullable();
            $table->integer('stamprate')->nullable();
            $table->bigInteger('user_id');
            $table->string('year', '10')->default(date('Y'));
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
