<?php
namespace Xultech\LaravelIpWhitelist;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Xultech\LaravelIpWhitelist\Directives\BladeDirectives;
use Xultech\LaravelIpWhitelist\Macros\RouteMacros;

class IpWhitelistServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ipwhitelist.php', 'ipwhitelist');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Xultech\LaravelIpWhitelist\Commands\AddIpCommand::class,
                \Xultech\LaravelIpWhitelist\Commands\RemoveIpCommand::class,
                \Xultech\LaravelIpWhitelist\Commands\ListIpCommand::class,
            ]);
        }
        $this->app->singleton('ipwhitelist', function () {
            return new \Xultech\LaravelIpWhitelist\Support\IpWhitelistService();
        });

    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/ipwhitelist.php' => config_path('ipwhitelist.php'),
        ], 'ipwhitelist-config');

        // Register middleware alias
        $this->app->booted(function () {
            $router = $this->app->make(Router::class);
            $router->aliasMiddleware('ipwhitelist', \Xultech\LaravelIpWhitelist\Middleware\IpWhitelistMiddleware::class);
        });

        // Load blade directives
        if (class_exists(Blade::class)) {
            BladeDirectives::register();
        }

        // Register route macros
        if (class_exists(Route::class)) {
            RouteMacros::register();
        }
        // Load migration
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}