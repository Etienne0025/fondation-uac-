<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NouvelUtilisateurMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nomComplet;
    public string $motDePasse;
    public string $email;

    public function __construct(string $nomComplet, string $motDePasse, string $email)
    {
        $this->nomComplet = $nomComplet;
        $this->motDePasse = $motDePasse;
        $this->email = $email;
        
    }

    public function build(): self
    {
        return $this->subject('Vos identifiants de connexion - FUAC Agenda')
                    ->view('emails.nouvel_utilisateur');
    }
}