<?php

declare(strict_types=1);

use App\Models\Preference;
use App\Models\User;

test('to array', function (): void {
    $preference = Preference::factory()->create()->fresh();

    expect(array_keys($preference->toArray()))
        ->toBe([
            'id',
            'user_id',
            'name',
            'value',
            'default_value',
            'created_at',
            'updated_at',
        ]);
});

test('updateOrCreatePreference creates a new preference', function (): void {
    $user = User::factory()->create();

    $preference = $user->updateOrCreatePreference('theme', 'dark', 'light');

    expect($preference->name)->toBe('theme')
        ->and($preference->value)->toBe('dark')
        ->and($preference->default_value)->toBe('light')
        ->and($user->preferences()->count())->toBe(1);
});

test('updateOrCreatePreference updates an existing preference without duplicating', function (): void {
    $user = User::factory()->create();

    $user->updateOrCreatePreference('theme', 'dark');
    $preference = $user->updateOrCreatePreference('theme', 'light');

    expect($preference->value)->toBe('light')
        ->and($user->preferences()->count())->toBe(1);
});

test('getPreference returns the stored value', function (): void {
    $user = User::factory()->create();
    $user->updateOrCreatePreference('theme', 'dark', 'light');

    expect($user->getPreference('theme'))->toBe('dark');
});

test('getPreference falls back to an empty string when missing', function (): void {
    $user = User::factory()->create();

    expect($user->getPreference('missing'))->toBe('');
});

test('deletePreference removes a preference', function (): void {
    $user = User::factory()->create();
    $user->updateOrCreatePreference('theme', 'dark');

    expect($user->deletePreference('theme'))->toBeTrue()
        ->and($user->preferences()->count())->toBe(0);
});

test('deletePreference returns false when nothing is deleted', function (): void {
    $user = User::factory()->create();

    expect($user->deletePreference('missing'))->toBeFalse();
});
