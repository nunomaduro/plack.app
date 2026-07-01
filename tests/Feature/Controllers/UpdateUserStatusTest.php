<?php

declare(strict_types=1);

use App\Models\User;

it('can set a status', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('user-status.update'), [
            'emoji' => '😀',
            'text' => 'Having fun',
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->status_emoji)->toBe('😀')
        ->and($user->status_text)->toBe('Having fun')
        ->and($user->status_expires_at)->toBeNull();
});

it('can set a status with expiration', function (): void {
    $user = User::factory()->create();
    $expiresAt = now()->addHour()->toISOString();

    $this->actingAs($user)
        ->patch(route('user-status.update'), [
            'emoji' => '🏠',
            'text' => 'Working from home',
            'expires_at' => $expiresAt,
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->status_emoji)->toBe('🏠')
        ->and($user->status_text)->toBe('Working from home')
        ->and($user->status_expires_at)->not->toBeNull();
});

it('can clear a status', function (): void {
    $user = User::factory()->create([
        'status_emoji' => '😀',
        'status_text' => 'Having fun',
    ]);

    $this->actingAs($user)
        ->patch(route('user-status.update'), [
            'emoji' => null,
            'text' => null,
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->status_emoji)->toBeNull()
        ->and($user->status_text)->toBeNull()
        ->and($user->status_expires_at)->toBeNull();
});

it('can update an existing status', function (): void {
    $user = User::factory()->create([
        'status_emoji' => '😀',
        'status_text' => 'Having fun',
    ]);

    $this->actingAs($user)
        ->patch(route('user-status.update'), [
            'emoji' => '🏖️',
            'text' => 'On vacation',
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->status_emoji)->toBe('🏖️')
        ->and($user->status_text)->toBe('On vacation');
});

it('validates emoji max length', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('user-status.update'), [
            'emoji' => str_repeat('a', 51),
        ])
        ->assertSessionHasErrors('emoji');
});

it('validates text max length', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('user-status.update'), [
            'text' => str_repeat('a', 101),
        ])
        ->assertSessionHasErrors('text');
});

it('validates expires_at must be in the future', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('user-status.update'), [
            'expires_at' => now()->subHour()->toISOString(),
        ])
        ->assertSessionHasErrors('expires_at');
});

it('requires authentication', function (): void {
    $this->patch(route('user-status.update'))
        ->assertRedirect(route('login'));
});
