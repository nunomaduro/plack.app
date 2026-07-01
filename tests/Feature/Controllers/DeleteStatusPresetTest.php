<?php

declare(strict_types=1);

use App\Models\StatusPreset;
use App\Models\User;

it('can delete own status preset', function (): void {
    $user = User::factory()->create();
    $preset = StatusPreset::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('status-preset.destroy', $preset))
        ->assertRedirect();

    expect(StatusPreset::query()->count())->toBe(0);
});

it('cannot delete another user status preset', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $preset = StatusPreset::factory()->for($otherUser)->create();

    $this->actingAs($user)
        ->delete(route('status-preset.destroy', $preset))
        ->assertNotFound();

    expect(StatusPreset::query()->count())->toBe(1);
});

it('requires authentication', function (): void {
    $preset = StatusPreset::factory()->create();

    $this->delete(route('status-preset.destroy', $preset))
        ->assertRedirect(route('login'));
});
