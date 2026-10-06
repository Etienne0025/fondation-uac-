<?php
// app/Notifications/TdrAvisNotification.php

namespace App\Notifications;

use App\Models\TdrAvis;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TdrAvisNotification extends Notification
{
    protected TdrAvis $avis;

    public function __construct(TdrAvis $avis)
    {
        $this->avis = $avis;
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $auteur = $this->avis->user;

        return (new WebPushMessage)
            ->title('Nouvel avis sur votre TDR')
            ->body("{$auteur->nom} {$auteur->prenom} a commenté : {$this->avis->tdr->titre}")
            ->icon('/image/logo-fuac.jpg')
            ->data([
                'type' => 'tdr_avis',
                'tdr_id' => $this->avis->tdr_id,
                'url' => '/tdr/' . $this->avis->tdr_id,
            ]);
    }
}