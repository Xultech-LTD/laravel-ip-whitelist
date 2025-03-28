<?php

use Illuminate\Support\Facades\DB;
use Xultech\LaravelIpWhitelist\Support\IpMatcher;

beforeEach(function () {
    DB::table('ip_whitelist_entries')->insert([
        ['ip' => '8.8.8.8', 'active' => true],
        ['ip' => '172.16.*.*', 'active' => true],
        ['ip' => '192.168.0.0/24', 'active' => true],
    ]);
});

it('matches exact IP from database', function () {
    expect(IpMatcher::isAllowed('8.8.8.8'))->toBeTrue();
});

it('matches wildcard IP from database', function () {
    expect(IpMatcher::isAllowed('172.16.5.5'))->toBeTrue();
});

it('matches CIDR IP from database', function () {
    expect(IpMatcher::isAllowed('192.168.0.200'))->toBeTrue();
});

it('does not match unknown IP from database', function () {
    expect(IpMatcher::isAllowed('123.45.67.89'))->toBeFalse();
});
