<?php

declare(strict_types=1);

namespace Tests;

use Appleton\Messages\Models\Message;
use Appleton\Messages\ServiceProvider;
use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Faker\Factory;
use Hash;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('mock-permissions.uuids', true);

        $this->artisan('migrate:fresh', ['--database' => 'sqlite']);
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            \Appleton\TypedConfig\ServiceProvider::class,
            PermissionServiceProvider::class,
            \Appleton\SpatieLaravelPermissionMock\ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function getUserModel(): Model
    {
        $faker = Factory::create();

        $user = new class extends UserUuid {
            use HasUuids;

            protected $table = 'users';
            protected $guarded = [];

            public function messages()
            {
                return $this->morphMany(Message::class, 'recipient');
            }
        };

        $user->name = $faker->name();
        $user->email = $faker->email();
        $user->password = Hash::make('password');

        $user->save();

        return $user;
    }
}
