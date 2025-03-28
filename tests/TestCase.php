<?php

namespace Xultech\LaravelIpWhitelist\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Xultech\LaravelIpWhitelist\IpWhitelistServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            IpWhitelistServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
//        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32))); // 👈 Set app key
//
//        $app['config']->set('ipwhitelist.enabled', true);
//        $app['config']->set('ipwhitelist.storage', 'config');
//        $app['config']->set('ipwhitelist.whitelisted_ips', [
//            '127.0.0.1',
//            '10.0.0.1',
//            '192.168.*.*',
//            '10.10.0.0/16',
//        ]);

        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        // Use in-memory SQLite for DB tests
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('ipwhitelist.storage', 'database');
    }
    protected function setUp(): void
    {
        parent::setUp();

        // Load internal package migration (test only)
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

}
