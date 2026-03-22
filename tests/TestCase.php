<?php

declare(strict_types=1);

namespace Darinlarimore\StatamicImagehotspots\Tests;

use Darinlarimore\StatamicImagehotspots\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Statamic\Extend\Manifest;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Stache\Stores\UsersStore;
use Statamic\Statamic;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function getPackageProviders($app): array
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function resolveApplicationConfiguration($app): void
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('statamic.editions.pro', true);
        $app['config']->set('statamic.users.repository', 'file');
        $app['config']->set('statamic.stache.stores.users', [
            'class' => UsersStore::class,
            'directory' => __DIR__.'/__fixtures__/users',
        ]);
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app->make(Manifest::class)->manifest = [
            'darinlarimore/statamic-imagehotspots' => [
                'id' => 'darinlarimore/statamic-imagehotspots',
                'namespace' => 'Darinlarimore\\StatamicImagehotspots',
            ],
        ];
    }
}
