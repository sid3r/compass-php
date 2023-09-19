<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration {

	public function up()
	{
		Schema::create('categories', function(Blueprint $table) {
			$table->id();
			$table->string('name', 50);
			$table->unsignedInteger('parent_id')->unsigned()->default(0);
      $table->integer('order')->nullable();
			$table->text('description')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	 
	}

	public function down()
	{
		Schema::drop('categories');
	}
}