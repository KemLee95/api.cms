<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckNoReader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:noreader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily check and send email about posts that is have no read on that date';

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
        $noReader = Post::noReaderPost();

    }
}
