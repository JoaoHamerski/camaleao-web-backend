<?php

namespace Database\Seeders;

use App\Models\GarmentMatch;
use App\Models\GarmentSize;

class GarmentMatchSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $GARMENT_MATCH_QUANTITY = 5;

        for ($i = 0; $i < $GARMENT_MATCH_QUANTITY; $i++) {
            $garmentMatch = GarmentMatch::factory()->create();

            $garmentMatch->values()->createMany($this->getGarmentMatchValues());
            $garmentMatch->sizes()->attach($this->getGarmentMatchSizes());
        }
    }

    public function getGarmentMatchValues()
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

    public function getGarmentMatchSizes()
    {
        $SIZE_QUANTITY = $this->faker->numberBetween(1, GarmentSize::count());

        $garmentSizes = GarmentSize::inRandomOrder()->take($SIZE_QUANTITY)->get();

        return $garmentSizes->map(fn ($garmentSize) => [
            'garment_size_id' => $garmentSize->id,
            'value' => $this->faker->randomFloat(1, 0, 5)
        ]);
    }
}
