<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global IP Whitelisting Toggle
    |--------------------------------------------------------------------------
    |
    | This option lets you disable IP whitelisting entirely across your app,
    | without removing middleware or route protections. Set this to false
    | if you temporarily want to allow access from all IP addresses.
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Whitelisted IPs
    |--------------------------------------------------------------------------
    |
    | Here you can define the default IP addresses that should be allowed.
    | You may use:
    |  - Single IPs: '192.168.1.10'
    |  - CIDR notation: '192.168.0.0/24'
    |  - Wildcards: '10.0.*.*'
    |
    | You can also extend this list dynamically by calling the internal
    | facade or using a database driver in future versions.
    |
    */

    'whitelisted_ips' => [
        '127.0.0.1',
        '::1',
        // '192.168.0.0/24',
        // '10.10.*.*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allow IP Overrides via ENV
    |--------------------------------------------------------------------------
    |
    | If true, the package will also look for a comma-separated list of IPs
    | from the environment variable: IP_WHITELIST_OVERRIDE
    |
    | This is useful for deployment pipelines or temporary access control.
    |
    */

    'allow_env_override' => true,

    /*
    |--------------------------------------------------------------------------
    | Env Variable for IP Override
    |--------------------------------------------------------------------------
    |
    | The name of the environment variable that should be checked
    | if 'allow_env_override' is enabled.
    |
    */

    'env_key' => 'IP_WHITELIST_OVERRIDE',

    /*
    |--------------------------------------------------------------------------
    | Unauthorized Response
    |--------------------------------------------------------------------------
    |
    | When a request from a non-whitelisted IP is detected, you can configure
    | how the package should respond:
    |
    | Supported types:
    | - 'abort': return an HTTP error response (403 by default)
    | - 'redirect': redirect the user to a URL
    | - 'json': return a JSON error message
    |
    */

    'response' => [
        'type' => 'abort', // abort | redirect | json

        // Only used when type is 'abort'
        'status_code' => 403,
        'message' => 'Access denied. Your IP is not authorized.',

        // Only used when type is 'redirect'
        'redirect_to' => '/unauthorized',

        // Only used when type is 'json'
        'json' => [
            'message' => 'Your IP address is not whitelisted.',
            'code' => 403
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | Choose where the IP whitelist should be loaded from.
    | Supported: "config", "database"
    |
    */

    'storage' => 'database',

    /*
    |--------------------------------------------------------------------------
    | Database Table Configuration
    |--------------------------------------------------------------------------
    |
    | You can customize the table name used to store whitelisted IPs.
    | This is useful if you want to avoid naming conflicts.
    |
    */

    'table' => 'ip_whitelist_entries',

    // Optional prefix (e.g., 'custom_') will become: custom_ip_whitelist_entries
    'table_prefix' => '',

];
