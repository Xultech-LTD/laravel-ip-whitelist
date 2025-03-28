<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Xultech\LaravelIpWhitelist\Models\IpWhitelistEntry;

beforeEach(function () {
    config(['ipwhitelist.storage' => 'database']);
    DB::table('ip_whitelist_entries')->truncate();
});

it('adds a new IP using ipwhitelist:add', function () {
    Artisan::call('ipwhitelist:add', [
        'ip' => '203.0.113.1',
        '--store' => 'database',
    ]);

    expect(IpWhitelistEntry::where('ip', '203.0.113.1')->exists())->toBeTrue();
});

it('does not add duplicate IPs', function () {
    IpWhitelistEntry::create(['ip' => '203.0.113.2', 'active' => true]);

    Artisan::call('ipwhitelist:add', [
        'ip' => '203.0.113.2',
        '--store' => 'database',
    ]);

    expect(IpWhitelistEntry::where('ip', '203.0.113.2')->count())->toBe(1);
});

it('removes IP using ipwhitelist:remove', function () {
    IpWhitelistEntry::create(['ip' => '203.0.113.3', 'active' => true]);

    Artisan::call('ipwhitelist:remove', [
        'ip' => '203.0.113.3',
        '--store' => 'database',
    ]);

    expect(IpWhitelistEntry::where('ip', '203.0.113.3')->exists())->toBeFalse();
});

it('lists IPs using ipwhitelist:list', function () {
    IpWhitelistEntry::create(['ip' => '203.0.113.4', 'active' => true]);
    IpWhitelistEntry::create(['ip' => '192.168.0.1', 'active' => false]);

    Artisan::call('ipwhitelist:list', [
        '--store' => 'database',
    ]);

    $output = Artisan::output(); // 👈 move here

    expect($output)->toContain('203.0.113.4');
});
