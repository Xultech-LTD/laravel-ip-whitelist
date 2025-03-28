<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Define test route with IP whitelist middleware
    Route::middleware(['web', 'ipwhitelist'])->get('/db-protected', fn () => 'DB OK');

    // Insert test IPs into the database
    DB::table('ip_whitelist_entries')->insert([
        ['ip' => '123.123.123.123', 'active' => true],
        ['ip' => '10.20.*.*', 'active' => true],
        ['ip' => '192.168.50.0/24', 'active' => true],
        ['ip' => '1.2.3.4', 'active' => false], // inactive
    ]);
});

it('grants access to exact IP from database', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '123.123.123.123'])
        ->get('/db-protected')
        ->assertOk()
        ->assertSee('DB OK');
});

it('grants access to CIDR IP from database', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '192.168.50.9'])
        ->get('/db-protected')
        ->assertOk()
        ->assertSee('DB OK');
});

it('grants access to wildcard IP from database', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '10.20.8.55'])
        ->get('/db-protected')
        ->assertOk()
        ->assertSee('DB OK');
});

it('blocks inactive IP from database', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
        ->get('/db-protected')
        ->assertStatus(403);
});

it('blocks IP not found in database', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
        ->get('/db-protected')
        ->assertStatus(403);
});
