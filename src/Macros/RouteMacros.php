<?php

namespace Xultech\LaravelIpWhitelist\Macros;

use Illuminate\Support\Facades\Route;

class RouteMacros
{
    /**
     * Register the ipWhitelisted() route macro.
     *
     * Usage:
     * Route::get('/admin', fn () => 'ok')->ipWhitelisted();
     *
     * Applies the ipwhitelist middleware.
     */
    public static function register(): void
    {
        Route::macro('ipWhitelisted', function () {
            /** @var \Illuminate\Routing\Route $this */
            return $this->middleware('ipwhitelist');
        });
    }
}