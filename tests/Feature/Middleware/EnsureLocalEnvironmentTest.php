<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

it('returns a 404 for gated routes outside the local environment', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    $this->get(route('login'))->assertNotFound();
    $this->get(route('register'))->assertNotFound();
});

it('keeps the early access route available outside the local environment', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    $this->withoutMiddleware(PreventRequestForgery::class)
        ->post(route('early-access.store'), ['email' => 'taylor@laravel.com'])
        ->assertRedirect();
});

it('allows gated routes within the local environment', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    $this->get(route('login'))->assertOk();
});
