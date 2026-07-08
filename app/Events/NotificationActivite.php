<?php

namespace App\Events;

use App\Models\Activite;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationActivite implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $titre;
    public string $message;
    public string $type;
    public int    $userId;

    public function __construct(Activite $activite, string $type, string $message)
    {
        $this->titre   = $activite->titre;
        $this->message = $message;
        $this->type    = $type;
        $this->userId  = $activite->user_id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.activite';
    }
}