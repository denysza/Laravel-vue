<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Estimate extends Mailable
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
                    ->text('emails.estimate_' . $this->data['type'])
                    ->with([
                        'category'  => $this->data['category'],
                        'priority'  => $this->data['priority'],
                        'period'    => $this->data['period'],
                        'property'  => $this->data['property'],
                        'floors'    => $this->data['floors'],
                        'age'       => $this->data['age'],
                        'area'      => $this->data['area'],
                        'area_b'    => $this->data['area_b'],
                        'type_roof' => $this->data['type_roof'],
                        'type_wall' => $this->data['type_wall'],
                        'budget'    => $this->data['budget'],
                        'memo'      => $this->data['memo'],
                    ]);
    }
}
