<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('fr_FR');
        
        // parents
        $parents = array();;
        for ($i = 1; $i < 6; $i++){
            $parents[] = [
                'name' => 'Category '.$i,
                'description' => $faker->sentence(6, true),
                'parent_id' => 0,
                'order' => $i
            ];         
        }
        DB::table('categories')->insert($parents);

        // children
        for ($i = 6; $i < 15; $i++){
            DB::table('categories')->insert([
                'name' => 'Category '.$i,
                'description' => $faker->sentence(6, true),
                'parent_id' => $faker->numberBetween(1,6),
                'order' => $i,
            ]);
        }
    }
}
