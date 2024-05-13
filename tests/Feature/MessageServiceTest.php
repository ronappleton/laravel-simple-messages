<?php

declare(strict_types=1);

namespace Tests\Feature;

use Appleton\Messages\Events\MessageDeleted;
use Appleton\Messages\Events\MessageRead;
use Appleton\Messages\Events\MessageSent;
use Appleton\Messages\Models\Message;
use Appleton\Messages\Services\Message as MessageService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function testSendCreatesAndDispatchesEvent(): void
    {
        Event::fake();

        $sender = new class extends User
        {
        };
        $sender->id = 1;

        $recipient = new class extends User
        {
        };
        $recipient->id = 2;

        $content = 'Hello, World!';

        $messageService = new MessageService();
        $messageService->send($recipient, $content, $sender);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'sender_type' => get_class($sender),
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
            'content' => $content,
        ]);

        Event::assertDispatched(MessageSent::class);
    }

    public function testMarkReadUpdatesAndDispatchesEvent(): void
    {
        Event::fake();

        $message = Message::factory()->create(['read_at' => null]);

        $messageService = new MessageService();
        $messageService->markRead($message);

        $this->assertNotNull($message->fresh()->read_at);

        Event::assertDispatched(MessageRead::class);
    }

    public function testDeleteRemovesAndDispatchesEvent(): void
    {
        Event::fake();

        $message = Message::factory()->create();

        $messageService = new MessageService();
        $messageService->delete($message);

        $this->assertSoftDeleted($message);

        Event::assertDispatched(MessageDeleted::class);
    }

    public function testRecordNotificationAddsNotification(): void
    {
        $message = Message::factory()->create(['notifications' => null]);
        $type = 'test';

        $messageService = new MessageService();
        $messageService->recordNotification($message, $type);

        $this->assertArrayHasKey($type, $message->fresh()->notifications);
    }
}
