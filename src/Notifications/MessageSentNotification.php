<?php

declare(strict_types=1);

namespace Appleton\Messages\Notifications;

use Appleton\Messages\Models\Message;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MessageSentNotification extends Notification
{
    private readonly string $url;
    public function __construct(Message $message)
    {
        $this->url = config()->string('messages.message_box_url', 'messages');
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'You have a new message.',
            'url' => $this->url,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => 'You have a new message.',
            'url' => $this->url,
        ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'You have a new message.',
            'url' => $this->url,
        ];
    }
}
