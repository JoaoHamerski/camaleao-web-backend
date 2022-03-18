<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Activitylog\ActivityLogStatus;

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
                ExpenseSeeder::class,
                ClientSeeder::class,
            ]);
        });
    }
}
