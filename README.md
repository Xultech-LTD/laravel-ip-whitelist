# Laravel IP Whitelist

A flexible, modern Laravel middleware package to protect your application using configurable IP whitelisting. Supports CIDR blocks, wildcards, exact IPs, and storage via config or database.

---

## ✨ Features

- ✅ Match IPs using:
    - Exact match (e.g. `123.45.67.89`)
    - Wildcard (e.g. `192.168.*.*`)
    - CIDR blocks (e.g. `10.0.0.0/24`)
- ✅ Configurable storage (config or database)
- ✅ Middleware protection
- ✅ Blade directives
- ✅ Route macros
- ✅ Facade + helper function
- ✅ Artisan commands for managing IPs

---

## 📦 Installation

```bash
composer require xultech/laravel-ip-whitelist
```
Laravel will auto-discover the service provider and alias.

---
## ⚙️ Configuration
Publish the config file:
```bash
php artisan vendor:publish --provider="Xultech\LaravelIpWhitelist\IpWhitelistServiceProvider" --tag=ipwhitelist-config
```
Or create a custom `config/ipwhitelist.php`:
```php
return [
    'enabled' => true,
    'storage' => 'config', // or 'database'

    'whitelisted_ips' => [
        '127.0.0.1',
        '192.168.*.*',
        '10.0.0.0/16',
    ],

    'table' => 'ip_whitelist_entries',
    'table_prefix' => '',

    'allow_env_override' => true,
    'env_key' => 'IP_WHITELIST_OVERRIDE',

    'response' => [
        'type' => 'abort', // redirect | json | abort
        'redirect_to' => '/unauthorized',
        'json' => [
            'message' => 'Your IP is not authorized.',
            'code' => 403,
        ],
        'status_code' => 403,
        'message' => 'Access denied. Your IP is not whitelisted.',
    ],
];
```

---
## 🛡️ Usage
### Middleware
Add globally in `Http\Kernel.php`:
```php
'ipwhitelist' => \Xultech\LaravelIpWhitelist\Middleware\IpWhitelistMiddleware::class,
```
For Laravel 11+:

Add to your `bootstrap/app.php` file:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'ipwhitelist' => \Xultech\LaravelIpWhitelist\Middleware\IpWhitelistMiddleware::class,
    ]);
})
```
Apply to a route:
```php
Route::middleware('ipwhitelist')->group(function () {
    Route::get('/admin', fn () => 'Protected');
});
```
### Route Macro
```php
Route::get('/admin', fn () => 'Protected')->ipWhitelisted();
```
### Blade Directive
```blade
@ipwhitelisted
    <p>This content is visible only to whitelisted IPs.</p>
@endipwhitelisted
```
### Facade
```php
use IpWhitelist;

if (IpWhitelist::isAllowed()) {
    // access granted
}
```
### Helper
```php
if (ip_whitelisted()) {
    // access granted
}
```
---
## 🧩 Using the Model
This package includes a built-in Eloquent model you can use to manage whitelisted IPs via database.
1. Run the migration:
No need to publish anything. The package automatically loads its migration.
    ```bash
    php artisan migrate
    ```
   This creates the `ip_whitelist_entries` table (or your custom name, based on config).
2. Use the model in your code:
    ```php
   use Xultech\LaravelIpWhitelist\Models\IpWhitelistEntry;
   
    IpWhitelistEntry::create([
        'ip' => '203.0.113.1',
        'active' => true,
    ]);

    // Query active entries
     $allowed = IpWhitelistEntry::where('active', true)->pluck('ip');
    ```
   You do not need to publish the model — it is fully usable directly from the package.
### 🛠 Customize the Table Name
In `config/ipwhitelist.php`, you can change:
```php
'table' => 'ip_whitelist_entries',
'table_prefix' => '',
```
To create a custom table name like:
```php
'table' => 'entries',
'table_prefix' => 'security_',
// Resolves to `security_entries`
```
---
## ⚡ Artisan Commands

| Command                      | Description                     |
|-----------------------------|---------------------------------|
| `ipwhitelist:add {ip}`      | Add IP to the whitelist         |
| `ipwhitelist:remove {ip}`   | Remove IP from the whitelist    |
| `ipwhitelist:list`          | List whitelisted IPs            |

Or, with example usage included directly under:
**Examples:**

```bash
php artisan ipwhitelist:add 203.0.113.1 --store=database
php artisan ipwhitelist:remove 203.0.113.1 --store=database
php artisan ipwhitelist:list --store=database
```

---
## 🧪 Testing

The package uses [Pest](https://pestphp.com) and [Orchestra Testbench](https://github.com/orchestral/testbench) for testing.

Run the test suite:

```bash
./vendor/bin/pest
```
## ✅ Requirements / Compatibility

- **PHP:** 7.3 – 8.4+
- **Laravel:** 6.x, 7.x, 8.x, 9.x, 10.x, 11.x, 12.x

> This package is tested across multiple Laravel versions and follows Laravel's release cycle. It works out of the box with both long-term support (LTS) and the latest Laravel versions.

## 📄 License

This package is open-sourced software licensed under the MIT license.

## 🤝 Contributing

Contributions are welcome and appreciated!

To contribute:

1. Fork this repository
2. Create a new branch for your feature or fix:
   ```bash
   git checkout -b feature/my-feature
   ```
3. Make your changes with clear, descriptive commits
4. Write or update tests if applicable
5. Run the test suite to make sure everything passes:
    ```bash
   ./vendor/bin/pest
    ```
6. Push your branch:
7. Open a Pull Request and describe your changes

## 🧭 Guidelines

- Follow **PSR-12** coding standards
- Keep pull requests **focused and minimal**
- For large changes, consider opening an issue first to discuss

Thank you for helping improve **Laravel IP Whitelist**!

## 👥 Credits & Authors

**Laravel IP Whitelist** was crafted with care by [Michael Erastus](https://github.com/michaelerastus) under [XulTech](https://github.com/Xultech-LTD) as part of our mission to build secure and developer-friendly Laravel tools.

### 🧑‍💻 Core Maintainer
- Michael Erastus — [GitHub](https://github.com/michaelerastus) · [Twitter/X](https://twitter.com/kopiumdev)

### 🤝 Contributors
Special thanks to everyone who provided feedback, reported issues, or helped shape the direction of this package. Your support makes open source better.

If you find this package helpful, consider giving it a ⭐️ on GitHub or sharing it with others in the Laravel community.

For contributions, ideas, or collaborations, feel free to reach out!

