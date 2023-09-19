<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');
        
        for ($i = 0; $i < 200; $i++){
          
          $price = $faker->numberBetween(200, 2000);

          DB::table('products')->insert([
              'name' => 'Product '.($i+1),
              'bar_code' => $faker->ean13(),
              'category_id' => $faker->numberBetween(1,14),
              'min_qty' => $faker->numberBetween(0,100),
              'description' => $faker->sentence(6, true),
              'discount' => $faker->randomElement($array = array (null, 5, 10, null)),
              'unit_price' => $price,
              'unit_sale_price' => $price + ($price * 25 / 100),
          ]);
        }
    }
}
