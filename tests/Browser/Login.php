<?php

declare(strict_types=1);

use App\Models\User;

it('fails with invalid credentials', function (): void {
    User::factory()->withoutTwoFactor()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'test@example.com')
        ->fill('password', 'wrong-password')
        ->click('@login-button')
        ->assertPresent('@input-error')
        ->assertPathIs('/login');
});

it('logs in with valid credentials', function (): void {
    User::factory()->withoutTwoFactor()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->click('@login-button')
        ->assertPathIs('/workspaces');
});
