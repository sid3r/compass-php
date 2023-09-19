<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');
        $date = Carbon::now()->subYear();

        for ($i = 1; $i < 30; $i++){

            $type = $faker->randomElement($array = array ('estimate','invoice'));
            $status = $faker->randomElement($array = array ('pending', 'finished', 'canceled', 'validated'));
            $code = $i;

            DB::table('documents')->insert([
                'type' => $type,
                'status' => $status,
                'code' => $code,
                'company_id' => $faker->numberBetween(1,20),
                'date' => $date->addDay(rand(0, 10)),
                'year' => $date->format('Y'),
                'vatrate' => $faker->randomElement($array = array (0,19)),
                'stamprate' => $faker->randomElement($array = array (0,1)),
                'user_id' => $faker->numberBetween(1,10),
            ]);
        }
    }
}
