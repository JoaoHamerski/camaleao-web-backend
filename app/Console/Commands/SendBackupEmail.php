<?php

namespace App\Console\Commands;

use App\Mail\BackupMade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBackupEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:send-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a backup e-mail notification.';

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
        Mail::to('joaohamerski2@gmail.com')->send(new BackupMade());
    }
}
