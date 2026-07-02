<?php

declare(strict_types=1);

it('renders the favicon and web app manifest tags', function (): void {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('<link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96">', false)
        ->assertSee('<link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">', false)
        ->assertSee('<link rel="shortcut icon" href="/favicon/favicon.ico">', false)
        ->assertSee('<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">', false)
        ->assertSee('<meta name="apple-mobile-web-app-title" content="'.config('app.name').'">', false)
        ->assertSee('<link rel="manifest" href="/favicon/site.webmanifest">', false);
});

it('serves the referenced favicon assets', function (): void {
    $files = [
        'favicon-96x96.png',
        'favicon.svg',
        'favicon.ico',
        'apple-touch-icon.png',
        'site.webmanifest',
    ];

    foreach ($files as $file) {
        expect(public_path("favicon/{$file}"))->toBeFile();
    }
});
