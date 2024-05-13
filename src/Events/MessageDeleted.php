<?php

declare(strict_types=1);

namespace Appleton\Messages\Events;

use Appleton\Messages\Models\Message;

readonly class MessageDeleted
{
    public function __construct(private Message $message)
    {
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
