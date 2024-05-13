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
        Schema::create('users', function ($table) {
            $table->id();
            $table->timestamps();
        });

        $userClass = new class extends Model {
            protected $table = 'users';
        };

        $sender = $userClass::query()->create();
        $recipient = $userClass::query()->create();

        $messageService->send($recipient, 'Hello, World!', $sender);

        Event::assertDispatched(MessageSent::class, function ($event) {
            return $event->getMessage()->content === 'Hello, World!';
        });
    }

    public function testGetMessageFromMessageReadEvent(): void
    {
        Event::fake();

        $messageService = new MessageService();
        Schema::create('users', function ($table) {
            $table->id();
            $table->timestamps();
        });

        $userClass = new class extends Model {
            protected $table = 'users';

            public function messages()
            {
                return $this->morphMany(Message::class, 'recipient');
            }
        };

        $sender = $userClass::query()->create();
        $recipient = $userClass::query()->create();

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
        Schema::create('users', function ($table) {
            $table->id();
            $table->timestamps();
        });

        $userClass = new class extends Model {
            protected $table = 'users';

            public function messages()
            {
                return $this->morphMany(Message::class, 'recipient');
            }
        };

        $sender = $userClass::query()->create();
        $recipient = $userClass::query()->create();

        $messageService->send($recipient, 'Hello, World!', $sender);
        $messageService->delete($recipient->messages->first());

        Event::assertDispatched(MessageDeleted::class, function ($event) {
            return $event->getMessage()->content === 'Hello, World!';
        });
    }
}