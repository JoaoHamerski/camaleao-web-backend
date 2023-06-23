<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
                ExpenseTypeSeeder::class,
                ProductTypeSeeder::class,
                ModelSeeder::class,
                MaterialSeeder::class,
                NeckTypeSeeder::class,
                SleeveTypeSeeder::class,
                GarmentSizeSeeder::class,
                GarmentMatchSeeder::class,
                ClothingTypeSeeder::class,
                ExpenseSeeder::class,
                ClientSeeder::class,
                SectorSeeder::class,
                StatusSeeder::class,
                OrderSeeder::class,
                PaymentSeeder::class,
                NoteSeeder::class,
            ]);
        });
    }
}
