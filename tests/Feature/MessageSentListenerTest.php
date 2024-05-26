<?php

declare(strict_types=1);

namespace Tests\Feature;

use Appleton\Messages\Listeners\MessageSentListener;
use Appleton\Messages\Notifications\MessageSentNotification;
use Appleton\Messages\Services\Message;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(MessageSentNotification::class)]
#[CoversClass(MessageSentListener::class)]
class MessageSentListenerTest extends TestCase
{
    public function testMessageSentListener(): void
    {
        $messageService = new Message;

        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

        Notification::fake();

        $messageService->send($recipient, 'Test message', $sender);

        Notification::assertSentTo($recipient, MessageSentNotification::class);
    }
}