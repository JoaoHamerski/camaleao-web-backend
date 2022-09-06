<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class SyncOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync order status for order control';

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
        activity()->withoutLogs(function () {
            $orders = Order::whereNull('closed_at')->get();

            $this->withProgressBar($orders, function (Order $order) {
                $order->syncStatus();
            });
        });
    }
}
