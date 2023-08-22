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
    protected $signature = 'orders:status-sync';

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

        $bar = $this->output->createProgressBar($orders->count());

        activity()->withoutLogs(function () use ($orders, $bar) {
            $orders->each(function ($order) use ($bar) {
                $this->confirmExistingStatus($order);
                $this->syncAllStatus($order);

                $bar->advance();
            });
        });

        $bar->finish();

        return 0;
    }


    public function syncAllStatus($order)
    {
        $order->linkedStatus()->syncWithoutDetaching(Status::all()->pluck('id'));
    }

    public function confirmExistingStatus($order)
    {
        $order->linkedStatus->each(function ($status) use ($order) {
            $order->linkedStatus()->updateExistingPivot($status->id, [
                'is_confirmed' => true
            ]);
        });
    }
}
