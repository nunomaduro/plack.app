<?php

declare(strict_types=1);

use App\Actions\DeleteWorkspace;
use App\Models\Channel;
use App\Models\Message;
use App\Models\Workspace;

it('may delete a workspace', function (): void {
    $workspace = Workspace::factory()->create();

    $action = resolve(DeleteWorkspace::class);

    $action->handle($workspace);

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse();
});

it('deletes the workspace channels and their messages', function (): void {
    $workspace = Workspace::factory()->create();
    $channels = Channel::factory()->count(2)->for($workspace)->create();
    $messages = $channels->flatMap(fn (Channel $channel) => Message::factory()->count(2)->for($channel)->create());

    $action = resolve(DeleteWorkspace::class);

    $action->handle($workspace);

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse();

    $channels->each(function (Channel $channel): void {
        expect(Channel::query()->whereKey($channel->id)->exists())->toBeFalse();
    });

    $messages->each(function (Message $message): void {
        expect(Message::query()->whereKey($message->id)->exists())->toBeFalse();
    });
});
