<?php

use Xultech\LaravelIpWhitelist\Support\IpMatcher;



it('matches exact IP', function () {
    config(['ipwhitelist.storage' => 'config']);
    expect(IpMatcher::isAllowed('127.0.0.1'))->toBeTrue();
});

it('matches wildcard IP', function () {
    config(['ipwhitelist.storage' => 'config']);
    config(['ipwhitelist.whitelisted_ips' => ['192.168.*.*']]);
    expect(IpMatcher::isAllowed('192.168.1.22'))->toBeTrue();
});

it('matches CIDR IP', function () {
    config(['ipwhitelist.storage' => 'config']);
    config(['ipwhitelist.whitelisted_ips' => ['10.10.0.0/16']]);
    expect(IpMatcher::isAllowed('10.10.45.67'))->toBeTrue();
});

it('fails for non-whitelisted IP', function () {
    expect(IpMatcher::isAllowed('8.8.8.8'))->toBeFalse();
});
