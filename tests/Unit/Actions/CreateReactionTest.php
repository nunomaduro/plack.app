<?php

declare(strict_types=1);

use App\Actions\CreateReaction;
use App\Enums\Emoji;
use App\Models\Channel;
use App\Models\Reaction;
use App\Models\User;
use App\Models\Workspace;

it('may create a reaction', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->for(Workspace::factory()->for($user, 'owner'))->create();

    $reaction = resolve(CreateReaction::class)->handle($user, $channel, Emoji::ThumbsUp);

    expect($reaction)
        ->toBeInstanceOf(Reaction::class)
        ->and($reaction->user->id)->toBe($user->id)
        ->and($reaction->emoji)->toBe(Emoji::ThumbsUp)
        ->and($reaction->reactable_type)->toBe($channel->getMorphClass())
        ->and($reaction->reactable_id)->toBe($channel->id);
});

it('does not duplicate the same reaction', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->for(Workspace::factory()->for($user, 'owner'))->create();

    $action = resolve(CreateReaction::class);

    $first = $action->handle($user, $channel, Emoji::ThumbsUp);
    $second = $action->handle($user, $channel, Emoji::ThumbsUp);

    expect($first->id)->toBe($second->id)
        ->and(Reaction::query()->count())->toBe(1);
});

it('allows different emojis on the same target', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->for(Workspace::factory()->for($user, 'owner'))->create();

    $action = resolve(CreateReaction::class);

    $action->handle($user, $channel, Emoji::ThumbsUp);
    $action->handle($user, $channel, Emoji::Heart);

    expect(Reaction::query()->count())->toBe(2);
});

it('allows different users to react with the same emoji', function (): void {
    $channel = Channel::factory()->create();

    $action = resolve(CreateReaction::class);

    $action->handle(User::factory()->create(), $channel, Emoji::ThumbsUp);
    $action->handle(User::factory()->create(), $channel, Emoji::ThumbsUp);

    expect(Reaction::query()->count())->toBe(2);
});
