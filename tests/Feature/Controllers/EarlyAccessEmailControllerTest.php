<?php

declare(strict_types=1);

use App\Models\EarlyAccessEmail;
use Inertia\Support\SessionKey;

it('stores an early access email', function (): void {
    $response = $this->post(route('early-access.store'), [
        'email' => 'taylor@laravel.com',
    ]);

    $response->assertRedirect()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __("You're on the list. We'll be in touch."),
            ],
        ]);

    expect(EarlyAccessEmail::query()->where('email', 'taylor@laravel.com')->exists())->toBeTrue();
});

it('validates the email', function (): void {
    $response = $this->post(route('early-access.store'), [
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors('email');

    expect(EarlyAccessEmail::query()->count())->toBe(0);
});

it('requires an email', function (): void {
    $response = $this->post(route('early-access.store'), []);

    $response->assertSessionHasErrors('email');

    expect(EarlyAccessEmail::query()->count())->toBe(0);
});

it('rejects a duplicate email', function (): void {
    EarlyAccessEmail::factory()->create(['email' => 'taylor@laravel.com']);

    $response = $this->post(route('early-access.store'), [
        'email' => 'taylor@laravel.com',
    ]);

    $response->assertSessionHasErrors('email');

    expect(EarlyAccessEmail::query()->where('email', 'taylor@laravel.com')->count())->toBe(1);
});
