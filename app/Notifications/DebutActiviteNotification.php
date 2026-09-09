<?php

namespace App\Notifications;

use App\Models\Activite;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class DebutActiviteNotification extends Notification
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
        return (new WebPushMessage)
            ->title($this->activite->titre)
            ->body("L'activité commence maintenant — {$this->activite->lieu}")
            ->icon('/image/logo-fuac.jpg')
            ->data([
                'type'            => 'debut',
                'notification_id' => $this->notificationId,
                'activite_id'     => $this->activite->id,
                'url'             => '/responsable',
            ]);
    }
}