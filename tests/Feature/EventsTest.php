<?php

declare(strict_types=1);

namespace Tests\Feature;

use Appleton\Messages\Events\MessageDeleted;
use Appleton\Messages\Events\MessageRead;
use Appleton\Messages\Events\MessageSent;
use Appleton\Messages\Models\Message;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Appleton\Messages\Services\Message as MessageService;
use Illuminate\Database\Eloquent\Model;

class EventsTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetMessageFromMessageSentEvent(): void
    {
        Event::fake();

        $messageService = new MessageService();

        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

        $messageService->send($recipient, 'Hello, World!', $sender);

        Event::assertDispatched(MessageSent::class, function ($event) {
            return $event->getMessage()->content === 'Hello, World!';
        });
    }

    public function testGetMessageFromMessageReadEvent(): void
    {
        Event::fake();

        $messageService = new MessageService();

        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

        $messageService->send($recipient, 'Hello, World!', $sender);
        $messageService->markRead($recipient->messages->first());

        Event::assertDispatched(MessageRead::class, function ($event) {
            return $event->getMessage()->content === 'Hello, World!';
        });
    }

    public function testGetMessageFromMessageDeletedEvent(): void
    {
        Event::fake();

        $messageService = new MessageService();

        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

        $messageService->send($recipient, 'Hello, World!', $sender);
        $messageService->delete($recipient->messages->first());

        Event::assertDispatched(MessageDeleted::class, function ($event) {
            return $event->getMessage()->content === 'Hello, World!';
        });
    }
}