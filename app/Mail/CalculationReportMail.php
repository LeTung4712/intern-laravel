<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CalculationReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $calculations;
    public $user;

    public function __construct($calculations, $user)
    {
        $this->calculations = $calculations;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Calculation Report - Last 10 Minutes')
            ->view('emails.calculation_report')
            ->with([
                'calculations' => $this->calculations,
                'user' => $this->user,
            ]);
    }
}
