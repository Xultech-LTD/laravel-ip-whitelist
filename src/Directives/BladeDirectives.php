<?php

namespace Xultech\LaravelIpWhitelist\Directives;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request;
use Xultech\LaravelIpWhitelist\Support\IpMatcher;

class BladeDirectives
{
    /**
     * Register @ipwhitelisted and @endipwhitelisted directives.
     */
    public static function register(): void
    {
        Blade::directive('ipwhitelisted', function () {
            return "<?php if (\\Xultech\\LaravelIpWhitelist\\Directives\\BladeDirectives::check()): ?>";
        });

        Blade::directive('endipwhitelisted', function () {
            return "<?php endif; ?>";
        });
    }

    /**
     * Determine if the current IP is allowed via IP matcher.
     *
     * @return bool
     */
    public static function check(): bool
    {
        // Use the IP matcher which now supports config or database
        return IpMatcher::isAllowed(Request::ip());
    }
}