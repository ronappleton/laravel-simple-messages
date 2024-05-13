<?php

declare(strict_types=1);

namespace Appleton\Messages\Models;

use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read string $id
 * @property string $sender_id
 * @property string $sender_type
 * @property string $recipient_id
 * @property string $recipient_type
 * @property string $sent_at
 * @property string $read_at
 * @property array $notifications
 * @property string $content
 * @property string $deleted_at
 */
class Message extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'recipient_id',
        'recipient_type',
        'sent_at',
        'read',
        'read_at',
        'notifications',
        'content',
        'deleted_at',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
            'notifications' => 'array',
        ];
    }

    protected static function newFactory(): MessageFactory
    {
        return MessageFactory::new();
    }

    public function sender(): MorphTo
    {
        return $this->morphTo('sender');
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo('recipient');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
