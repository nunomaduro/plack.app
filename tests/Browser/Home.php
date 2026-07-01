<?php

declare(strict_types=1);

it('has the landing page', function (): void {
    $page = visit('/');

    $page->assertPathIs('/')
        ->assertNoJavaScriptErrors();
});

it('shows a login action on the landing page', function (): void {
    $page = visit('/');

    $page->click('@login-link')
        ->assertPathIs('/login');
});

it('shows a register action on the landing page', function (): void {
    $page = visit('/');

    $page->click('@register-link')
        ->assertPathIs('/register');
});
