<?php

declare(strict_types=1);

namespace Tests\Feature;

use Appleton\Messages\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Schema;
use Tests\TestCase;

class MessageModelTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetSender(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->timestamps();
        });

        $userClass = new class extends Model {
            protected $table = 'users';
        };

        $sender = $userClass::query()->create();

        $recipient = $userClass::query()->create();

        $message = Message::factory()->create([
            'sender_id' => $sender->id,
            'sender_type' => get_class($sender),
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
        ]);

        $this->assertEquals($sender->id, $message->sender->id);
    }

    public function testGetRecipient(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->timestamps();
        });

        $userClass = new class extends Model {
            protected $table = 'users';
        };

        $sender = $userClass::query()->create();

        $recipient = $userClass::query()->create();

        $message = Message::factory()->create([
            'sender_id' => $sender->id,
            'sender_type' => get_class($sender),
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
        ]);

        $this->assertEquals($recipient->id, $message->recipient->id);
    }

    public function testGetUnreadMessages(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->timestamps();
        });

        $userClass = new class extends Model {
            protected $table = 'users';
        };

        $sender = $userClass::query()->create();

        $recipient = $userClass::query()->create();

        $message = Message::factory()->create([
            'sender_id' => $sender->id,
            'sender_type' => get_class($sender),
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
            'read_at' => null,
        ]);

        $this->assertCount(1, Message::unread()->get());
    }

    public function testGetReadMessages(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->timestamps();
        });

        $userClass = new class extends Model {
            protected $table = 'users';
        };

        $sender = $userClass::query()->create();

        $recipient = $userClass::query()->create();

        Message::factory()->create([
            'sender_id' => $sender->id,
            'sender_type' => get_class($sender),
            'recipient_id' => $recipient->id,
            'recipient_type' => get_class($recipient),
            'read_at' => now(),
        ]);

        $this->assertCount(1, Message::read()->get());
    }
}