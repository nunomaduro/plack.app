<?php

declare(strict_types=1);

it('has the landing page', function (): void {
    $page = visit('/');

    $page->assertPathIs('/')
        ->assertNoJavaScriptErrors();
});

it('does not show a login action on the landing page', function (): void {
    $page = visit('/');

    $page->assertMissing('@login-link');
});

it('does not show a register action on the landing page', function (): void {
    $page = visit('/');

    $page->assertMissing('@register-link');
});
