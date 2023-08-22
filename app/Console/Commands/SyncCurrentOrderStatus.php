<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Status;
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
        $orders = Order::whereNull('closed_at');

        activity()->withoutLogs(function () use ($orders) {
            $orders->each(function ($order) {
                $this->confirmExistingStatus($order);
                $this->syncAllStatus($order);
            });
        });

        return 0;
    }


    public function syncAllStatus($order)
    {
        $order->syncWithoutDetaching(Status::all()->pluck('id'));
    }

    public function confirmExistingStatus($order)
    {
        $order->linkedStatus->each(function ($status) use ($order) {
            $order->linkedStatus->updateExistingPivot($status->id, [
                'is_confirmed' => true
            ]);
        });
    }
}
