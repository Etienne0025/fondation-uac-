<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class AlerteWebPush extends Notification
{
    protected string $titre;
    protected string $message;
    protected ?string $url;

    public function __construct(string $titre, string $message, ?string $url = '/responsable')
    {
        $this->titre = $titre;
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->titre)
            ->icon('/image/logo-fuac.jpg')
            ->body($this->message)
            ->action('Voir', 'voir')
            ->data(['url' => $this->url])
            ->options(['TTL' => 3600]);
    }
}