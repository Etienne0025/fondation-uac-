<?php

namespace App\Notifications;

use App\Models\Activite;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class RappelActiviteNotification extends Notification
{
    protected Activite $activite;
    protected int $notificationId;

    public function __construct(Activite $activite, int $notificationId)
    {
        $this->activite = $activite;
        $this->notificationId = $notificationId;
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $dateHeure = $this->activite->date->translatedFormat('d/m/Y à H:i');

        return (new WebPushMessage)
            ->title($this->activite->titre)
            ->body("Prévu le {$dateHeure}")
            ->icon('/image/logo-fuac.jpg')
            ->data([
                'type'            => 'rappel',
                'notification_id' => $this->notificationId,
                'activite_id'     => $this->activite->id,
                'url'             => '/responsable',
            ]);
    }
}