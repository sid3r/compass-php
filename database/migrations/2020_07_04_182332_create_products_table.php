<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->id();
			$table->string('name', 64)->unique();
			$table->string('bar_code', 25)->nullable();
			$table->bigInteger('category_id')->unsigned();
      $table->text('description')->nullable();
      $table->string('image')->default('default-product.jpg');
			$table->integer('min_qty')->nullable();
      $table->decimal('discount', 20,2)->nullable();
      $table->decimal('unit_price', 20,2);
      $table->decimal('unit_sale_price', 20,2);
			$table->string('status', 25)->default('active');
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('products');
	}
}