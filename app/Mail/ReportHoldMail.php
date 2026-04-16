<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportHoldMail extends Mailable
{
    use SerializesModels;

    public $patient;
    public $orderNumber;

    public function __construct($patient, $orderNumber)
    {
        $this->patient = $patient;
        $this->orderNumber = $orderNumber;
    }

    public function build()
    {
        return $this->subject('Your Laboratory Report is Ready for Collection')
            ->view('emails.report_hold');
    }
}
