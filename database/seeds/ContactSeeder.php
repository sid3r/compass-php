<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');
        $functions = array("Directeur", "Comptable", "Commercial", "Chargé de la production", "Chargé de la communication");
        // seed contacts
        for ($i = 1; $i < 20; $i++){

            DB::table('contacts')->insert([
                'name' => $faker->name,
                'company_id' => $i,
                'mobile' => $faker->e164PhoneNumber,
                'email' => $faker->safeEmail(),
                'function' => $faker->randomElement($functions),
            ]);
        }
    }
}
