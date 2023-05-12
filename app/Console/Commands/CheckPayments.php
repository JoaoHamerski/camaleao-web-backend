<?php

namespace App\Console\Commands;

use App\Models\BankEntry;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check';

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
        $bankEntries = BankEntry::all();

        $bankEntries->each(function ($bankEntry) {
            $entries = collect(json_decode(Storage::get($bankEntry->path), true));

            $entries->each(function ($entry) {
                $payment = Payment::where('bank_uid', $entry['bank_uid'])->first();

                if (!$payment) {
                    return;
                }

                if (!$this->datesMatch($entry, $payment)) {
                    $this->info($payment->id);
                }
            });
        });

        return 0;
    }

    public function datesMatch($entry, $payment)
    {
        $entryDate = $entry['date'];
        $paymentDate = Carbon::createFromFormat('Y-m-d', $payment->date)
            ->format('d/m/Y');

        return $entryDate === $paymentDate;
    }
}
