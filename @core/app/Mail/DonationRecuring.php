<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;

class DonationRecuring extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $subject;


    public function __construct($data,$subject)
    {
        $this->data = $data;
        $this->subject = $subject;
    }

    public function build()
    {
        $mail = $this->from(get_static_option('site_global_email'), get_static_option('site_title'))
                    ->subject($this->subject)
                    ->markdown('mail.recuring-donation');

        return $mail;
    }
}
