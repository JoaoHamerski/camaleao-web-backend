<?php

namespace Database\Seeders;

use App\Models\ClothMatch;
use App\Models\ClothSize;
use App\Models\ClothValue;
use App\Models\Material;
use App\Models\Model;
use App\Models\NeckType;
use App\Models\SleeveType;

class ClothMatchSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $CLOTH_MATCH_QUANTITY = 5;

        for ($i = 0; $i < $CLOTH_MATCH_QUANTITY; $i++) {
            $clothMatch = ClothMatch::factory()->create();

            $clothMatch->values()->createMany($this->getClothMatchValues());
            $clothMatch->sizes()->attach($this->getClothMatchSizes());
        }
    }

    public function getClothMatchValues()
    {
        $NUMBER_OF_INTERVALS = $this->faker->numberBetween(2, 5);
        $intervals = [];

        for ($i = 0; $i < $NUMBER_OF_INTERVALS; $i++) {
            $start = $i === 0 ? 0 : $intervals[$i - 1]['end'] + 1;
            $end = $i === 0
                ? $this->faker->numberBetween(1, 10)
                : $this->faker->numberBetween($start + 1, ($start + 1)  + 10);

            $value = $this->faker->randomFloat(1, 10, 30);

            if ($i === $NUMBER_OF_INTERVALS - 1) {
                $end = null;
            }

            $intervals[] = compact('start', 'end', 'value');
        }

        return $intervals;
    }

    public function getClothMatchSizes()
    {
        $SIZE_QUANTITY = $this->faker->numberBetween(1, ClothSize::count());

        $clothSizes = ClothSize::inRandomOrder()->take($SIZE_QUANTITY)->get();

        return $clothSizes->map(fn ($clothSize) => [
            'cloth_size_id' => $clothSize->id,
            'value' => $this->faker->randomFloat(1, 0, 5)
        ]);
    }
}
