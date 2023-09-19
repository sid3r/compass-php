<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StorehousesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker::create('fr_FR');
      
      // seed storehouses
      DB::table('storehouses')->insert([
        [
          'name' => 'Bejaia',
          'code' => 'DPBJ',
          'adress' => $faker->address,
        ],
        [
          'name' => 'El Kseur',
          'code' => 'DPEK',
          'adress' => $faker->address,
        ],
        [
          'name' => 'Akbou',
          'code' => 'DPAK',
          'adress' => $faker->address,
        ]
      ]);
      // seed storehouses users
      DB::table('storehouse_users')->insert([
        [
          'storehouse_id' => 1,
          'user_id' => 1
        ],
        [
          'storehouse_id' => 1,
          'user_id' => 2
        ],
        [
          'storehouse_id' => 1,
          'user_id' => 3
        ],
        [
          'storehouse_id' => 2,
          'user_id' => 4
        ],
        [
          'storehouse_id' => 2,
          'user_id' => 5
        ],
        [
          'storehouse_id' => 2,
          'user_id' => 6
        ],
      ]);
    }
}
