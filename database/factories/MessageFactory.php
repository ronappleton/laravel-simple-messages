<?php

declare(strict_types=1);

namespace Database\Factories;

use Appleton\Messages\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => $this->faker->sentence(),
            'sender_type' => $this->faker->sentence(),
            'recipient_id' => $this->faker->sentence(),
            'recipient_type' => $this->faker->sentence(),
            'sent_at' => null,
            'read_at' => null,
            'notifications' => null,
            'content' => $this->faker->sentence(),
            'deleted_at' => null,
        ];
    }
}
