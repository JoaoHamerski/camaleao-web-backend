<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

class RoleSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles são inseridas nos migrations "create_roles_table",
        // pois é importante que sua estrutura fique como está,
        // inclusive em testes, sem modificações, apenas adições se necessário.
    }
}
