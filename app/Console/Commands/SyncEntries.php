<?php

namespace App\Console\Commands;

use App\Models\BankEntry;
use App\Models\Entry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entries:sync';

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
        $entries = BankEntry::all();

        foreach ($entries as $entry) {
            $path = $entry->path;
            $data = collect(json_decode(Storage::get($path)));

            $data = $data->map(function ($item) {
                $item->via_id = 3;
                return $item;
            });

            Storage::put($path, json_encode($data));
        }

        Entry::query()->update(['via_id' => 3]);

        return 0;
    }
}
