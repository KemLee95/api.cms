<?php

namespace App\Console\Commands;

use App\Jobs\PickEmailFromInactiveUsersJob;
use App\Models\OutgoingEmailTracking;
use Illuminate\Console\Command;

class PickPendingEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pick:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command to pick pending email';

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
        $pendingEmail = OutgoingEmailTracking::whereNull("deleted_at")->where("status", "pending")->get();
        
        for($idx=0;$idx < count($pendingEmail); $idx ++) {
            $outgoingEmail = $pendingEmail[$idx];
            PickEmailFromInactiveUsersJob::dispatch($outgoingEmail)->onConnection('database');
        }
    }
}
