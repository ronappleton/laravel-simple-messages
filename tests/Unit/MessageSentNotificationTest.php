<?php

declare(strict_types=1);

namespace Tests\Unit;

use Appleton\Messages\Models\Message;
use Appleton\Messages\Notifications\MessageSentNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(MessageSentNotification::class)]
class MessageSentNotificationTest extends TestCase
{
    private Message $message;

    protected function setUp(): void
    {
        parent::setUp();

        $sender = $this->getUserModel();
        $recipient = $this->getUserModel();

        $this->message = Message::factory()->create([
            'sender_id' => $sender->id,
            'sender_type' => $sender::class,
            'recipient_id' => $recipient->id,
            'recipient_type' => $recipient::class,
            'content' => 'This is a test message.',
        ]);

        Config::set('messages.message_box_url', 'http://localhost/message-box');
    }

    public function testDatabaseMessageIsCorrect(): void
    {
        $notification = new MessageSentNotification($this->message);

        $message = $notification->toDatabase(new \stdClass());

        $this->assertIsArray($message);

        $this->assertEquals('You have a new message.', $message['message']);
        $this->assertEquals('http://localhost/message-box', $message['url']);
    }

    public function testBroadcastMessageIsCorrect(): void
    {
        $notification = new MessageSentNotification($this->message);

        $message = $notification->toBroadcast(new \stdClass());

        $this->assertInstanceOf(BroadcastMessage::class, $message);
        $this->assertIsArray($message->data);

        $this->assertEquals('You have a new message.', $message->data['message']);
        $this->assertEquals('http://localhost/message-box', $message->data['url']);
    }

    public function testArrayMessageIsCorrect(): void
    {
        $notification = new MessageSentNotification($this->message);

        $message = $notification->toArray(new \stdClass());

        $this->assertIsArray($message);

        $this->assertEquals('You have a new message.', $message['message']);
        $this->assertEquals('http://localhost/message-box', $message['url']);
    }

    public function testViaMethodReturnsCorrectArray(): void
    {
        $notification = new MessageSentNotification($this->message);

        $this->assertEquals(['database', 'broadcast'], $notification->via(new \stdClass()));
    }
}