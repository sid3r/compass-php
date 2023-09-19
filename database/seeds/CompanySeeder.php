<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');
        $activities = array("Publicité", "Finances", "Immobilier", "Architecture", "Construction", "I.T");
        $regions = array("Alger", "Béjaïa", "Mostaghanem", "Biskra", "Setif", "Oran", "Batna", "Souk Ahras", "Tlemcen", "Bouira", "Tizi Ouzou");
        // seed tags
        DB::table('tags')->insert([
          [
            'name' => 'client',
            'color' => 'success',
            'native' => 'yes',
          ],
          [
            'name' => 'supplier',
            'color' => 'primary',
            'native' => 'yes',
          ],
          [
            'name' => 'prospect',
            'color' => 'info',
            'native' => 'yes',
          ]
        ]);

        for ($i = 1; $i <= 20; $i++){

            DB::table('companies')->insert([
                'name' => $faker->company,
                'tel' => $faker->e164PhoneNumber,
                'fax' => $faker->e164PhoneNumber,
                'email' => $faker->safeEmail,
                'address' => $faker->address,
                'region' => $faker->randomElement($regions),
                'activity' => $faker->randomElement($activities),
                'rc' => $faker->creditCardNumber,
                'ai' => $faker->creditCardNumber,
                'nif' => $faker->creditCardNumber,
            ]);
            DB::table('company_tags')->insert([
                'company_id' => $i,
                'tag_id' => $faker->randomElement($array = array (1,2,3)),
            ]);
        }
    }
}