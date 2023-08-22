<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncCurrentOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order-status:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os pedidos atuais com a atualização feita.';

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
        return 0;
    }
}
