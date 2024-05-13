<?php

declare(strict_types=1);

namespace Appleton\Messages\Services;

use Appleton\Messages\Events\MessageDeleted;
use Appleton\Messages\Events\MessageRead;
use Appleton\Messages\Events\MessageSent;
use Appleton\Messages\Models\Message as MessageModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message
{
    public function send(Model $recipient, string $content, ?Model $sender = null): void
    {
        $sender ??= auth()->user();

        /** @var Model $sender */
        DB::transaction(function () use ($recipient, $content, $sender) {
            $message = MessageModel::query()
                ->create(
                    [
                        'sender_id' => $sender->getAttribute('id'),
                        'sender_type' => $sender::class,
                        'recipient_id' => $recipient->getAttribute('id'),
                        'recipient_type' => $recipient::class,
                        'content' => $content,
                        'sent_at' => Carbon::now(),
                    ]
                );

            event(new MessageSent($message));
        });
    }

    public function markRead(MessageModel $message): void
    {
        DB::transaction(function () use ($message) {
            $message->update(['read_at' => Carbon::now()]);
            event(new MessageRead($message));
        });
    }

    public function delete(MessageModel $message): void
    {
        DB::transaction(function () use ($message) {
            $message->delete();
            event(new MessageDeleted($message));
        });
    }

    public function recordNotification(MessageModel $message, string $type): void
    {
        $notifications = $message->getAttribute('notifications') ?? [];

        $notifications[$type] = Carbon::now();

        $message->update(['notifications' => $notifications]);
    }
}
