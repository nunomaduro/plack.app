<?php

declare(strict_types=1);

use App\Actions\ParseMentions;
use App\Models\User;

it('parses mentioned names from message body', function (): void {
    $user = User::factory()->create(['name' => 'John Doe']);

    $result = resolve(ParseMentions::class)->handle('Hello @John.Doe!');

    expect($result)->toHaveCount(1)
        ->and($result->first()->id)->toBe($user->id);
});

it('parses multiple mentions', function (): void {
    $john = User::factory()->create(['name' => 'John Doe']);
    $jane = User::factory()->create(['name' => 'Jane Smith']);

    $result = resolve(ParseMentions::class)->handle('Hello @John.Doe and @Jane.Smith!');

    expect($result)->toHaveCount(2)
        ->and($result->pluck('id')->sort()->values()->all())
        ->toBe(collect([$john->id, $jane->id])->sort()->values()->all());
});

it('deduplicates mentions', function (): void {
    User::factory()->create(['name' => 'John Doe']);

    $result = resolve(ParseMentions::class)->handle('@John.Doe said hi to @John.Doe');

    expect($result)->toHaveCount(1);
});

it('returns empty collection when no mentions exist', function (): void {
    $result = resolve(ParseMentions::class)->handle('Hello world!');

    expect($result)->toBeEmpty();
});

it('ignores mentions of non-existent users', function (): void {
    $result = resolve(ParseMentions::class)->handle('Hello @Nonexistent.User!');

    expect($result)->toBeEmpty();
});

it('does not match email addresses', function (): void {
    User::factory()->create(['name' => 'John']);

    $result = resolve(ParseMentions::class)->handle('Email me at user@john.com');

    expect($result)->toBeEmpty();
});
