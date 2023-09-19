<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed modules
        $this->call([
            UsersTableSeeder::class,
            ConfigSeeder::class,
            CompanySeeder::class,
            ContactSeeder::class,
            DocumentSeeder::class,
            DocumentItemsSeeder::class,
            CategoriesSeeder::class,
            ProductsSeeder::class,
            StorehousesSeeder::class,
            PurchasesSeeder::class,
        ]);
    }
}
