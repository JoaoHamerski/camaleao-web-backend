<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $BRANCHES_QUANTITY = 5;

        Branch::factory()
            ->count($BRANCHES_QUANTITY)
            ->create();
    }
}
