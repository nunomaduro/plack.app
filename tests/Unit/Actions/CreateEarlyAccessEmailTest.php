<?php

declare(strict_types=1);

use App\Actions\CreateEarlyAccessEmail;
use App\Models\EarlyAccessEmail;

it('creates an early access email', function (): void {
    $action = resolve(CreateEarlyAccessEmail::class);

    $earlyAccessEmail = $action->handle('taylor@laravel.com');

    expect($earlyAccessEmail)->toBeInstanceOf(EarlyAccessEmail::class)
        ->and($earlyAccessEmail->email)->toBe('taylor@laravel.com')
        ->and($earlyAccessEmail->exists)->toBeTrue()
        ->and(EarlyAccessEmail::query()->where('email', 'taylor@laravel.com')->exists())->toBeTrue();
});
