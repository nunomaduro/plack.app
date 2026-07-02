<?php

declare(strict_types=1);

it('renders the favicon and web app manifest tags', function (): void {
    $response = $this->get(route('login'));

    $response->assertOk()->assertSeeHtml('<link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96">')->assertSeeHtml('<link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">')->assertSeeHtml('<link rel="shortcut icon" href="/favicon/favicon.ico">')->assertSeeHtml('<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">')->assertSeeHtml('<meta name="apple-mobile-web-app-title" content="'.config('app.name').'">')->assertSeeHtml('<link rel="manifest" href="/favicon/site.webmanifest">');
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
        expect(public_path('favicon/'.$file))->toBeFile();
    }
});
