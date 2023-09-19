<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PurchasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker::create('fr_FR');
      $date = Carbon::now();

      for ($i = 0; $i < 200; $i++){
        // store
        
        $store = $faker->numberBetween(1,2);
        $date = $date->subDay(rand(0, 10));
        $total = $faker->numberBetween(30000, 100000);

        // purchases
        $purchase = DB::table('purchases')->insertGetId([
          'company_id' => $faker->randomElement($array = array (null,rand(0, 10))),
          'storehouse_id' => $store,
          'total' => $total,
          'created_at' => $date
        ]);
        for ($j = 0; $j < $faker->numberBetween(10,20); $j++) {
          $product = $faker->numberBetween(1,200);
          $price = $faker->numberBetween(100, 5000);

          DB::table('purchase_items')->insert([
            'purchase_id' => $purchase,
            'product_id' => $product,
            'qty' => $faker->numberBetween(10,100),
            'created_at' => $date
          ]);
        }

        // sales
        for($s = 1; $s <=2; $s++){

          $total = $faker->numberBetween(30000, 100000);

          $sale = DB::table('sales')->insertGetId([
            'storehouse_id' => $s,
            'document_id' => $faker->randomElement($array = array (null,rand(0, 20))),
            'total' => $total + $total * rand(10, 30)/100,
            'profit' => ($total + $total * rand(10, 30) /100) - $total,
            'created_at' => $date
          ]);

          for ($j = 0; $j < $faker->numberBetween(10,20); $j++) {
            $product = $faker->numberBetween(1,200);
            $price = $faker->numberBetween(100, 5000);
  
            DB::table('sale_items')->insert([
              'sale_id' => $sale,
              'product_id' => $product,
              'qty' => $faker->numberBetween(1,10),
              'created_at' => $date
            ]);
          }
        }

      }
    }
}
