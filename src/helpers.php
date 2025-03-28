<?php

use Xultech\LaravelIpWhitelist\Support\IpMatcher;

if (!function_exists('ip_whitelisted')) {
    /**
     * Check if the current IP is whitelisted.
     *
     * @param string|null $ip
     * @return bool
     */
    function ip_whitelisted(?string $ip = null): bool
    {
        return IpMatcher::isAllowed($ip ?? request()->ip());
    }
}
