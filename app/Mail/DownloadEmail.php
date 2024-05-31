<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DownloadEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $email;
    public $guid;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $guid, $type)
    {
        $this->email = $email;
        $this->guid = $guid;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pathToFile = storage_path("app/{$this->type}/{$this->guid}.zip");

        return $this->subject('Download Email')
            ->view('emails.download')
            ->attach($pathToFile);
    }
}