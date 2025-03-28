<?php

namespace Xultech\LaravelIpWhitelist\Support;

class IpWhitelistService
{
    /**
     * Proxy the check to the static IpMatcher.
     *
     * @param string|null $ip
     * @return bool
     */
    public function isAllowed(?string $ip = null): bool
    {
        return IpMatcher::isAllowed($ip ?? request()->ip());
    }

    /**
     * Return a list of resolved allowed IPs (based on config or DB).
     *
     * @return array
     * @throws \ReflectionException
     */
    public function all(): array
    {
        $ref = new \ReflectionClass(IpMatcher::class);
        $method = $ref->getMethod('getAllowedIps');
        $method->setAccessible(true);

        return $method->invoke(null);
    }
}