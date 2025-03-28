<?php

use Illuminate\Support\Facades\Route;
use Xultech\LaravelIpWhitelist\Tests\TestCase;

beforeEach(function () {
    Route::middleware(['web', 'ipwhitelist'])
        ->get('/protected', fn () => 'Access granted');
});

it('grants access to whitelisted IP', function () {
    config(['ipwhitelist.storage' => 'config']); // 👈 Force correct driver
    config(['ipwhitelist.whitelisted_ips' => ['10.0.0.1']]); // 👈 Ensure this IP is listed

    $this
        ->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->get('/protected')
        ->assertOk()
        ->assertSee('Access granted');

});

it('blocks non-whitelisted IP', function () {
    $this
        ->withServerVariables(['REMOTE_ADDR' => '203.0.113.55'])
        ->get('/protected')
        ->assertStatus(403);
});
