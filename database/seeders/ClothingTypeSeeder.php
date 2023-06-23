<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClothingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * @deprecated Mantido ainda para garantir retrocompatibilidade com pedidos antigos
         *
         * Os tipos iniciais de roupas são estáticos e já são
         * inseridos no migration "create_clothing_types_table".
         * Aqui só é populado os dados deles e da comissão.
         */
    }
}
