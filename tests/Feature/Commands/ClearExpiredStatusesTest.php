<?php

declare(strict_types=1);

use App\Models\User;

it('clears expired statuses', function (): void {
    $user = User::factory()->create([
        'status_emoji' => '😀',
        'status_text' => 'Having fun',
        'status_expires_at' => now()->subMinute(),
    ]);

    $this->artisan('status:clear-expired')
        ->assertSuccessful();

    $user->refresh();

    expect($user->status_emoji)->toBeNull()
        ->and($user->status_text)->toBeNull()
        ->and($user->status_expires_at)->toBeNull();
});

it('does not clear active statuses with future expiration', function (): void {
    $user = User::factory()->create([
        'status_emoji' => '😀',
        'status_text' => 'Having fun',
        'status_expires_at' => now()->addHour(),
    ]);

    $this->artisan('status:clear-expired')
        ->assertSuccessful();

    $user->refresh();

    expect($user->status_emoji)->toBe('😀')
        ->and($user->status_text)->toBe('Having fun');
});

it('does not clear statuses without expiration', function (): void {
    $user = User::factory()->create([
        'status_emoji' => '😀',
        'status_text' => 'Having fun',
        'status_expires_at' => null,
    ]);

    $this->artisan('status:clear-expired')
        ->assertSuccessful();

    $user->refresh();

    expect($user->status_emoji)->toBe('😀')
        ->and($user->status_text)->toBe('Having fun');
});
