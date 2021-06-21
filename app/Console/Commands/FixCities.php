<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix cities issues';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cities = DB::table('clients')
            ->orderBy('city', 'asc')
            ->whereNotNull('city')
            ->select('city')
            ->distinct()
            ->get()
            ->pluck('city');

        $this->output->newLine();
        $this->info('Cadastrando e atualizando usuários com as cidades da nova tabela "cities"...');

        $citiesBar = $this->output->createProgressBar(count($cities));
        $citiesBar->start();

        foreach ($cities as $city) {
            $newCity = City::create(['name' => $city]);

            Client::where('city', $city)->update(['city_id' => $newCity->id]);
            
            $citiesBar->advance();
        }

        $this->output->newLine();
        $citiesBar->finish();
    }
}
