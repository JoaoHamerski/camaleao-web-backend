<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\SectorsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        activity()->withoutLogs(function () {
            $this->call([
                UserSeeder::class,
                CitySeeder::class,
                ShippingCompanySeeder::class,
                BranchSeeder::class,
                ClothingTypeSeeder::class,
                ExpenseTypeSeeder::class,
                ProductTypeSeeder::class,
                ExpenseSeeder::class,
                ClientSeeder::class,
                SectorsSeeder::class,
                StatusSeeder::class,
                OrdersSeeder::class,
                PaymentsSeeder::class,
                NotesSeeder::class,
                ModelSeeder::class,
                MaterialSeeder::class,
                NeckTypeSeeder::class,
                SleeveTypeSeeder::class,
                GarmentSizeSeeder::class,
                GarmentMatchSeeder::class
            ]);
        });
    }
}
