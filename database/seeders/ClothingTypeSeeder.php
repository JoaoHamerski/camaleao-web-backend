<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use App\Models\AppConfig;

class ClothingTypeSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Os tipos iniciais de roupas são estáticos e já são
         * inseridos no migration "create_clothing_types_table".
         * Aqui só é populado os dados deles e da comissão.
         */

        ClothingType::each(function (ClothingType $clothingType) {
            $clothingType->update([
                'commission' => round($this->faker->randomFloat(2, 10, 20), 1)
            ]);
        });

        AppConfig::set(
            'orders',
            'print_commission',
            round($this->faker->randomFloat(2, 30, 50), 1)
        );
    }
}
