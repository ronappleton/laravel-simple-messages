<?php

declare(strict_types=1);

namespace Appleton\Messages\Listeners;

use Appleton\Messages\Events\MessageSent;
use Appleton\Messages\Notifications\MessageSentNotification;

class MessageSentListener
{
    public function handle(MessageSent $event): void
    {
        $message = $event->getMessage();

        $user = $message->recipient;

        $user->notify(new MessageSentNotification($message));
    }
}