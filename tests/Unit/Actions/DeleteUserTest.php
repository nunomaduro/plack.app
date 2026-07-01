<?php

declare(strict_types=1);

use App\Actions\DeleteUser;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;

it('may delete a user', function (): void {
    $user = User::factory()->create();

    $action = resolve(DeleteUser::class);

    $action->handle($user);

    expect($user->exists)->toBeFalse();
});

it('deletes the user owned workspaces, channels, and every message the user sent', function (): void {
    $user = User::factory()->create();

    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    $ownedMessage = Message::factory()->for($channel)->for($user, 'user')->create();

    $otherUser = User::factory()->create();
    $otherWorkspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $otherChannel = Channel::factory()->for($otherWorkspace)->create();
    $sentElsewhere = Message::factory()->for($otherChannel)->for($user, 'user')->create();

    $action = resolve(DeleteUser::class);

    $action->handle($user);

    expect(User::query()->whereKey($user->id)->exists())->toBeFalse()
        ->and(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse()
        ->and(Channel::query()->whereKey($channel->id)->exists())->toBeFalse()
        ->and(Message::query()->whereKey($ownedMessage->id)->exists())->toBeFalse()
        ->and(Message::query()->whereKey($sentElsewhere->id)->exists())->toBeFalse()
        ->and(User::query()->whereKey($otherUser->id)->exists())->toBeTrue()
        ->and(Workspace::query()->whereKey($otherWorkspace->id)->exists())->toBeTrue()
        ->and(Channel::query()->whereKey($otherChannel->id)->exists())->toBeTrue();
});
