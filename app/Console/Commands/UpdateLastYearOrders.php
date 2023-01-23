<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Status;
use Illuminate\Console\Command;

class UpdateLastYearOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:last-year-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $status = Status::find('21');
        $ordersQuantity = Order::whereYear('created_at', '2022')->count();

        $this->info($status);
        $this->info('QUANTIDADE DE PEDIDOS: ' . $ordersQuantity);
        $confirmation = $this->confirm('Atualizar pedidos de 2022 por este status?');

        if (!$confirmation) {
            return;
        }

        Order::whereYear('created_at', '2022')
            ->update(['status_id' => $status->id]);

        return 0;
    }
}
