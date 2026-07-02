<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateChannel;
use App\Actions\DeleteChannel;
use App\Actions\MarkChannelAsRead;
use App\Actions\UpdateChannel;
use App\Http\Requests\CreateChannelRequest;
use App\Http\Requests\DeleteChannelRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use App\Queries\ListChannels;
use App\Queries\ListWorkspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ChannelController
{
    public function show(
        #[CurrentUser] User $user,
        Workspace $workspace,
        Channel $channel,
        ListWorkspace $listWorkspace,
        ListChannels $listChannels,
        MarkChannelAsRead $markChannelAsRead,
    ): Response {
        $markChannelAsRead->handle($channel, $user);

        $channel->load(['messages' => fn (HasMany $messages) => $messages->oldest()->with('sender')]);

        return Inertia::render('channel/show', [
            'workspace' => [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'slug' => $workspace->slug,
                'channels' => $listChannels->get($user, $workspace),
            ],
            'channel' => $channel,
            'messages' => $channel->messages->map(fn (Message $message): array => [
                'id' => $message->id,
                'body' => $message->body,
                'sender' => $message->sender->name,
                'createdAt' => $message->created_at->toIso8601String(),
            ]),
            'workspaces' => $listWorkspace->get($user),
            'canManage' => $user->is($workspace->owner),
        ]);
    }

    public function store(
        CreateChannelRequest $request,
        Workspace $workspace,
        CreateChannel $createChannel,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $channel = $createChannel->handle($workspace, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Channel created.'),
        ]);

        return to_route('channel.show', [$workspace, $channel]);
    }

    public function update(
        UpdateChannelRequest $request,
        Workspace $workspace,
        Channel $channel,
        UpdateChannel $updateChannel,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $updateChannel->handle($channel, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Channel updated.'),
        ]);

        return to_route('channel.show', [$workspace, $channel]);
    }

    public function destroy(
        DeleteChannelRequest $request,
        Workspace $workspace,
        Channel $channel,
        DeleteChannel $deleteChannel,
    ): RedirectResponse {
        $deleteChannel->handle($channel);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Channel deleted.'),
        ]);

        return to_route('workspace.show', $workspace);
    }
}
