<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('const.contact.send_to'), mb_encode_mimeheader(config('const.contact.sender')))
                    ->subject($this->data['subject'])
                    ->text('emails.contact_' . $this->data['type'])
                    ->with([
                        'name'     => $this->data['name'],
                        'contents' => $this->data['contents'],
                    ]);
    }
}
