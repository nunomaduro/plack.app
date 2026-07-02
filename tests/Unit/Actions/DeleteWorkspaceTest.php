<?php

declare(strict_types=1);

use App\Actions\DeleteWorkspace;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Support\Facades\DB;

it('deletes a workspace with its members and invitations', function (): void {
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach(User::factory()->create());
    WorkspaceInvitation::factory()->for($workspace)->create();

    resolve(DeleteWorkspace::class)->handle($workspace);

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse()
        ->and(DB::table('workspace_user')->count())->toBe(0)
        ->and(WorkspaceInvitation::query()->count())->toBe(0);
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
