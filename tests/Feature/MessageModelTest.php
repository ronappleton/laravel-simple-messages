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
        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

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

        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

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

        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

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
        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

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