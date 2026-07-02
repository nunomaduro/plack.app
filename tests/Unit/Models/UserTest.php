<?php

declare(strict_types=1);

use App\Models\User;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'two_factor_confirmed_at',
            'created_at',
            'updated_at',
            'avatar',
        ]);
});

test('avatar returns ui avatars url with user name', function (): void {
    $user = User::factory()->create(['name' => 'John Doe']);

    expect($user->avatar)
        ->toContain('https://ui-avatars.com/api/?')
        ->toContain('name=John+Doe');
});

test('avatar color is consistent for the same user', function (): void {
    $user = User::factory()->create();

    expect($user->avatar)->toBe($user->avatar);
});

test('avatar is the same for users with the same name', function (): void {
    $userA = User::factory()->create(['name' => 'Same Name', 'email' => 'alice@example.com']);
    $userB = User::factory()->create(['name' => 'Same Name', 'email' => 'bob@example.com']);

    expect($userA->avatar)->toBe($userB->avatar);
});

test('avatar differs for users with different names', function (): void {
    $userA = User::factory()->create(['name' => 'Alice Smith']);
    $userB = User::factory()->create(['name' => 'Bob Jones']);

    expect($userA->avatar)->not->toBe($userB->avatar);
});
