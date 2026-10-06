<?php

namespace App\Notifications;

use App\Models\Tdr;
use App\Models\User;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TdrApprouveNotification extends Notification
{
    protected Tdr $tdr;
    protected User $approuvePar;

    public function __construct(Tdr $tdr, User $approuvePar)
    {
        $this->tdr = $tdr;
        $this->approuvePar = $approuvePar;
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('TDR approuvé')
            ->body("{$this->approuvePar->nom} a approuvé : {$this->tdr->titre}")
            ->icon('/image/logo-fuac.jpg')
            ->data([
                'type' => 'tdr_approuve',
                'tdr_id' => $this->tdr->id,
                'url' => '/tdr/' . $this->tdr->id,
            ]);
    }
}