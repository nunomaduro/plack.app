<?php

declare(strict_types=1);

use App\Models\User;

it('validates the password confirmation', function (): void {
    $page = visit('/register');

    $page->fill('name', 'Test User')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'different-password')
        ->click('@register-user-button')
        ->assertPresent('@input-error')
        ->assertPathIs('/register');

    expect(User::query()->count())->toBe(0);
});

it('validates a unique email', function (): void {
    User::factory()->create(['email' => 'test@example.com']);

    $page = visit('/register');

    $page->fill('name', 'Test User')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->click('@register-user-button')
        ->assertPresent('@input-error')
        ->assertPathIs('/register');

    expect(User::query()->count())->toBe(1);
});

it('registers a new account', function (): void {
    $page = visit('/register');

    $page->fill('name', 'Test User')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->click('@register-user-button')
        ->assertPathIs('/verify-email');

    expect(User::query()->where('email', 'test@example.com')->exists())->toBeTrue();
});
