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
            ['name' => 'Atendimento'],
            ['name' => 'Design'],
            ['name' => 'Análise'],
            ['name' => 'Exportação'],
            ['name' => 'Produção'],
            ['name' => 'Costura'],
            ['name' => 'Conferência, entrega e envio'],
            ['name' => 'Gerencia'],
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
