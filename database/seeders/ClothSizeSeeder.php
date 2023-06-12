<?php

namespace Database\Seeders;

use App\Models\ClothSize;

class ClothSizeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClothSize::insert([
            ['name' => 'P', 'order' => 0],
            ['name' => 'M', 'order' => 1],
            ['name' => 'G', 'order' => 2],
            ['name' => 'GG', 'order' => 3],
            ['name' => 'XG', 'order' => 4],
        ]);
    }
}
