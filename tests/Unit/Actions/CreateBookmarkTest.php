<?php

declare(strict_types=1);

use App\Actions\CreateBookmark;
use App\Models\Bookmark;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;

it('may bookmark a message', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->for(Workspace::factory()->for($user, 'owner'))->create();
    $message = Message::factory()->for($channel)->for($user, 'sender')->create();

    $bookmark = resolve(CreateBookmark::class)->handle($user, $message);

    expect($bookmark)
        ->toBeInstanceOf(Bookmark::class)
        ->and($bookmark->user->id)->toBe($user->id)
        ->and($bookmark->message->id)->toBe($message->id);
});

it('does not duplicate a bookmark for the same message', function (): void {
    $user = User::factory()->create();
    $message = Message::factory()->for($user, 'sender')->create();

    $action = resolve(CreateBookmark::class);
    $first = $action->handle($user, $message);
    $second = $action->handle($user, $message);

    expect($first->id)->toBe($second->id)
        ->and(Bookmark::query()->count())->toBe(1);
});
