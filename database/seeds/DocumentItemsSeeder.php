<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DocumentItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');

        for ($i = 0; $i < 100; $i++){

            DB::table('document_items')->insert([
                'document_id' => $faker->numberBetween(1,29),
                'designation' => $faker->sentence(6, true),
                'quantity' => $faker->randomFloat($nbMaxDecimals = 2, $min = 10, $max = 30),
                'unit' => $faker->randomElement($array = array ("U","Kg","L")),
                'unit_price' => $faker->randomFloat($nbMaxDecimals = 2, $min = 1000, $max = 5000),
                'discount' => $faker->randomElement($array = array (0,5,7,10,20)),
            ]);
        }
    }
}
