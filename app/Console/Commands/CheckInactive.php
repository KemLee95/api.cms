<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\ReminderMailForUser;
use App\Jobs\SendReminderMailForUser;

class CheckInactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check and send email who not logged in for 1 day';

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
    public function handle() {

        $inactiveUser = User::getInactiveUser();
        foreach($inactiveUser as $key => $user) {
            $toAddress = $user->email;
            $name = $user->user_name;
 
            $mail = new ReminderMailForUser($name);

            SendReminderMailForUser::dispatch($toAddress, $mail);
        }
    }
}
