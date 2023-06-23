<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\Material;
use App\Models\NeckType;
use App\Models\SleeveType;
use App\Models\GarmentMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class GarmentMatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GarmentMatch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'model_id' => Model::inRandomOrder()->first()->id,
            'material_id' => $this->faker->boolean(100)
                ? Material::inRandomOrder()->first()->id
                : null,
            'neck_type_id' => $this->faker->boolean(100)
                ? NeckType::inRandomOrder()->first()->id
                : null,
            'sleeve_type_id' => $this->faker->boolean(100)
                ? SleeveType::inRandomOrder()->first()->id
                : null
        ];
    }
}
