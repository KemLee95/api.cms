<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Post;

class MailForAdmins extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        //
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $name = $this->name;
        $posts = Post::noReaderPost();
        return $this->view('mails.report-daily', compact("name", "posts"));
    }
}
