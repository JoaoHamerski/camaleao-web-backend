<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sectors = [
            ['name' => 'Pedidos para Analisar', 'alias' => 'Pedidos Cadastrados'],
            ['name' => 'Pedidos para Exportar', 'alias' => 'Pedidos Analisados'],
            ['name' => 'Pedidos para Imprimir', 'alias' => 'Pedidos Exportados'],
            ['name' => 'Pedidos para Cortar e Estampar', 'alias' => 'Pedidos Impressos'],
            ['name' => 'Pedidos para Costurar e Embalar', 'alias' => 'Pedidos Estampados'],
            ['name' => 'Pedidos no Estoque', 'alias' => 'Pedidos Costurados'],
        ];

        Sector::factory()->createMany($sectors);

        $this->attachUsersToSectors();
    }

    private function attachUsersToSectors()
    {
        $sectors = Sector::all();

        foreach ($sectors as $sector) {
            $sector->users()->attach(User::first());
        }
    }
}
