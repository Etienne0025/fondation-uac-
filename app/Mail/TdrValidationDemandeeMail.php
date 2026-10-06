<?php

namespace App\Mail;

use App\Models\Tdr;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TdrValidationDemandeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Tdr $tdr) {}

    public function build()
    {
        return $this->subject('Validation requise : ' . $this->tdr->titre)
            ->view('emails.tdr-validation-demandee');
    }
}