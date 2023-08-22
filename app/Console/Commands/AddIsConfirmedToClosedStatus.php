<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class AddIsConfirmedToClosedStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:add-is-confirmed';

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
        $orders = Order::whereNotNull('closed_at');

        activity()->withoutLogs(function () use ($orders) {
            $orders->each(function ($order) {
                $status = json_decode($order->final_status, true);

                foreach ($status as $key => $s) {
                    unset($status[$key]['pivot']['is_auto_concluded']);
                    unset($status[$key]['pivot']['created_at']);
                    $status[$key]['pivot']['is_confirmed'] = true;
                }

                $order->update(['final_status' => json_encode($status)]);
            });
        });

        return 0;
    }
}
