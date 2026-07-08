<?php

namespace App\Mail;

use App\Models\Activite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlerteActiviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public Activite $activite;

    public function __construct(Activite $activite)
    {
        $this->activite = $activite;
    }

    public function build(): self
    {
        return $this->subject('Alerte : ' . $this->activite->titre . ' non réalisée')
                    ->view('emails.alerte_activite');
    }
}