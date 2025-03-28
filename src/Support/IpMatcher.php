<?php

namespace Xultech\LaravelIpWhitelist\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IpMatcher
{
    /**
     * Check if the given IP is allowed based on the configured storage.
     *
     * @param string $ip
     * @return bool
     */
    public static function isAllowed(string $ip): bool
    {
        $allowedIps = self::getAllowedIps();

        foreach ($allowedIps as $allowed) {
            $allowed = trim($allowed);

            if ($allowed === '') {
                continue;
            }

            // Exact IP match
            if (filter_var($allowed, FILTER_VALIDATE_IP) && $ip === $allowed) {
                return true;
            }

            // Wildcard match (e.g., 10.0.*.*)
            if (strpos($allowed, '*') !== false && self::matchWildcard($ip, $allowed)) {
                return true;
            }

            // CIDR match (e.g., 192.168.0.0/24)
            if (strpos($allowed, '/') !== false && self::matchCidr($ip, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve the list of allowed IPs from the active storage driver.
     *
     * @return array
     */
    protected static function getAllowedIps(): array
    {
        $source = config('ipwhitelist.storage', 'config');

        if ($source === 'database') {
            return self::loadFromDatabase();
        }

        return self::loadFromConfig();
    }

    /**
     * Load IPs from config + ENV override if enabled.
     *
     * @return array
     */
    protected static function loadFromConfig(): array
    {
        $allowed = config('ipwhitelist.whitelisted_ips', []);

        if (config('ipwhitelist.allow_env_override', false)) {
            $envKey = config('ipwhitelist.env_key', 'IP_WHITELIST_OVERRIDE');
            $envIps = explode(',', env($envKey, ''));
            $envIps = array_map('trim', $envIps);
            $allowed = array_merge($allowed, array_filter($envIps));
        }

        return $allowed;
    }

    /**
     * Load IPs from the database.
     *
     * @return array
     */
    protected static function loadFromDatabase(): array
    {
        if (!class_exists(\Illuminate\Support\Facades\Schema::class)) {
            return [];
        }

        $table = self::resolveTableName();

        if (!Schema::hasTable($table)) {
            return [];
        }

        return DB::table($table)
            ->where('active', true)
            ->pluck('ip')
            ->filter()
            ->unique()
            ->toArray();
    }
    /**
     * Build the full table name from config and prefix.
     *
     * @return string
     */
    protected static function resolveTableName(): string
    {
        $prefix = config('ipwhitelist.table_prefix', '');
        $table = config('ipwhitelist.table', 'ip_whitelist_entries');

        return $prefix . $table;
    }

    /**
     * Check wildcard IP match (e.g., 192.168.*.*).
     *
     * @param string $ip
     * @param string $pattern
     * @return bool
     */
    protected static function matchWildcard(string $ip, string $pattern): bool
    {
        $regex = str_replace(['.', '*'], ['\.', '[0-9]+'], $pattern);
        return (bool) preg_match('/^' . $regex . '$/', $ip);
    }

    /**
     * Check CIDR range match.
     *
     * @param string $ip
     * @param string $cidr
     * @return bool
     */
    protected static function matchCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = ~((1 << (32 - $mask)) - 1);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}