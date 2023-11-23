<?php

namespace Database\Seeders;

use App\Models\AppConfig;
use App\Models\Status;
use Illuminate\Database\Seeder;

class ConfigsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppConfig::set('dashboard', 'production', json_encode([
            'estampados_no_dia_id' => Status::inRandomOrder()->first()->id,
            'costurados_no_dia_id' => Status::inRandomOrder()->first()->id,
            'month_production_id' => Status::inRandomOrder()->first()->id,
            'late_orders_id' => Status::inRandomOrder()->first()->id,
            'waiting_for_withdrawal_id' => Status::inRandomOrder()->first()->id,
            'delivered_id' => Status::inRandomOrder()->first()->id,
        ]));
    }
}
