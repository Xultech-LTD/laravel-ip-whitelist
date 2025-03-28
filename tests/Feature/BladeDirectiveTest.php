<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

beforeEach(function () {
    config([
        'ipwhitelist.storage' => 'config',
        'ipwhitelist.whitelisted_ips' => ['123.123.*.*'],
    ]);

    // Define a test Blade view inline
    View::addNamespace('test-views', __DIR__ . '/../resources/views');

    Route::get('/blade-test', function () {
        return view('test-views::ip-check');
    })->middleware('ipwhitelist');
});

it('shows content for whitelisted IP using @ipwhitelisted directive', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '123.123.45.67'])
        ->get('/blade-test')
        ->assertSee('Allowed');
});

it('does not show content for blocked IP using @ipwhitelisted directive', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
        ->get('/blade-test')
        ->assertDontSee('Allowed');
});
