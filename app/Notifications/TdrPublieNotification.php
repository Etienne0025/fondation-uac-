<?php
// app/Notifications/TdrPublieNotification.php

namespace App\Notifications;

use App\Models\Tdr;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TdrPublieNotification extends Notification
{
    protected Tdr $tdr;

    public function __construct(Tdr $tdr)
    {
        $this->tdr = $tdr;
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Nouveau TDR publié')
            ->body("{$this->tdr->user->nom} a publié le thème : {$this->tdr->titre}")
            ->icon('/image/logo-fuac.jpg')
            ->data([
                'type' => 'tdr_publie',
                'tdr_id' => $this->tdr->id,
                'url' => '/tdr/' . $this->tdr->id,
            ]);
    }
}