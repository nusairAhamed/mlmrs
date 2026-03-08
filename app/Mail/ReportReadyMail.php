<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportReadyMail extends Mailable
{
    use SerializesModels;

    public $patient;
    public $orderNumber;
    public $reportUrl;

    public function __construct($patient, $orderNumber, $reportUrl)
    {
        $this->patient = $patient;
        $this->orderNumber = $orderNumber;
        $this->reportUrl = $reportUrl;
    }

    public function build()
    {
        return $this->subject('Your Laboratory Report is Ready')
            ->view('emails.report_ready');
    }
}