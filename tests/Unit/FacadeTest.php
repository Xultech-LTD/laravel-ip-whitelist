<?php

use Illuminate\Support\Facades\Facade;
use Xultech\LaravelIpWhitelist\Facades\IpWhitelist;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Facade::setFacadeApplication(app()); // bind facades in test env

    Config::set('ipwhitelist.storage', 'config');
    Config::set('ipwhitelist.whitelisted_ips', ['127.0.0.1', '192.168.*.*', '10.0.0.0/24']);
});

it('returns true for allowed IP via facade', function () {
    expect(IpWhitelist::isAllowed('127.0.0.1'))->toBeTrue();
    expect(IpWhitelist::isAllowed('192.168.1.100'))->toBeTrue();
    expect(IpWhitelist::isAllowed('10.0.0.99'))->toBeTrue();
});

it('returns false for blocked IP via facade', function () {
    expect(IpWhitelist::isAllowed('8.8.8.8'))->toBeFalse();
});
