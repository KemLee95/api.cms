<?php

namespace App\Jobs;

use App\Mail\ReminderMail;
use App\Models\OutgoingEmailTracking;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class PickEmailFromInactiveUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $outgoingEmail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(OutgoingEmailTracking $outgoingEmail)
    {
        //
        $this->outgoingEmail = $outgoingEmail->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public $timeout = 60;
    public $tries = 3;
    public $deleteWhenMissingModels = true;

    public function handle() {
        try {
            $email = new ReminderMail();
            Mail::to($this->outgoingEmail->email)->send($email);
        } catch(Exception $exception) {
            $this->failed($exception);
        } finally {
            $item = OutgoingEmailTracking::where("id", $this->outgoingEmail->id)->first();
            $item->update(["status"=> OutgoingEmailTracking::DONE_STATUS]);
        }
    }

    public function failed(Exception $e) {

        $item = OutgoingEmailTracking::where("id", $this->outgoingEmail->id)->first();
        $item->update(["status"=> OutgoingEmailTracking::ERROR_STATUS]);
    }
}
