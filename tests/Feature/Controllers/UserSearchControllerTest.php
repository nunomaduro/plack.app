<?php

declare(strict_types=1);

use App\Models\User;

it('searches users by name', function (): void {
    $user = User::factory()->create(['name' => 'Alice']);
    User::factory()->create(['name' => 'Bob']);

    $this->actingAs($user)
        ->getJson(route('user.search', ['q' => 'Bob']))
        ->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'Bob']);
});

it('searches users by email', function (): void {
    $user = User::factory()->create(['email' => 'alice@example.com']);
    $bob = User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

    $this->actingAs($user)
        ->getJson(route('user.search', ['q' => 'bob']))
        ->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonFragment(['id' => $bob->id]);
});

it('excludes the current user from search results', function (): void {
    $user = User::factory()->create(['name' => 'John Doe']);
    User::factory()->create(['name' => 'Jane Doe']);

    $this->actingAs($user)
        ->getJson(route('user.search', ['q' => 'Doe']))
        ->assertStatus(200)
        ->assertJsonCount(1)
        ->assertJsonMissing(['name' => 'John Doe'])
        ->assertJsonFragment(['name' => 'Jane Doe']);
});

it('returns empty results when no users match', function (): void {
    $user = User::factory()->create(['name' => 'John Doe']);

    $this->actingAs($user)
        ->getJson(route('user.search', ['q' => 'nonexistent']))
        ->assertStatus(200)
        ->assertJsonCount(0);
});

it('requires authentication', function (): void {
    $this->getJson(route('user.search', ['q' => 'John']))
        ->assertStatus(401);
});

it('limits search results', function (): void {
    $user = User::factory()->create(['name' => 'Alice']);
    User::factory()->count(15)->create(['name' => 'Bob']);

    $this->actingAs($user)
        ->getJson(route('user.search', ['q' => 'Bob']))
        ->assertStatus(200)
        ->assertJsonCount(10);
});
