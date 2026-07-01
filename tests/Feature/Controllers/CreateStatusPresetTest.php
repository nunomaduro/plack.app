<?php

declare(strict_types=1);

use App\Models\StatusPreset;
use App\Models\User;

it('can create a status preset', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('status-preset.store'), [
            'emoji' => '🏠',
            'text' => 'Working from home',
        ])
        ->assertRedirect();

    expect(StatusPreset::query()->count())->toBe(1);

    $preset = StatusPreset::query()->first();

    expect($preset->emoji)->toBe('🏠')
        ->and($preset->text)->toBe('Working from home')
        ->and($preset->user_id)->toBe($user->id);
});

it('requires emoji', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('status-preset.store'), [
            'text' => 'Working from home',
        ])
        ->assertSessionHasErrors('emoji');
});

it('requires text', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('status-preset.store'), [
            'emoji' => '🏠',
        ])
        ->assertSessionHasErrors('text');
});

it('validates emoji max length', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('status-preset.store'), [
            'emoji' => str_repeat('a', 51),
            'text' => 'Test',
        ])
        ->assertSessionHasErrors('emoji');
});

it('validates text max length', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('status-preset.store'), [
            'emoji' => '🏠',
            'text' => str_repeat('a', 101),
        ])
        ->assertSessionHasErrors('text');
});

it('requires authentication', function (): void {
    $this->post(route('status-preset.store'))
        ->assertRedirect(route('login'));
});
