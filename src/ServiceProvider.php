<?php

declare(strict_types=1);

namespace Appleton\Messages;

use Appleton\Messages\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/messages.php', 'messages');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/messages.php' => config_path('messages.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Event::listen(MessageSent::class, [Listeners\MessageSentListener::class, 'handle']);
    }
}
