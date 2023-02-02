<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Status;
use Illuminate\Console\Command;

class NullLastYearOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'null:last-year-orders';

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

    public function handle()
    {
        OrderStatus::join(
            'orders',
            'orders.id',
            '=',
            'order_status.order_id'
        )->whereYear('orders.created_at', '2022')
            ->whereBetween('order_status.created_at', [
                '2023-01-23 09:20:00', '2023-01-23 09:30:00'
            ])
            ->select('order_status.*')
            ->update(['created_at' => null]);

        return 0;
    }
}
