<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', fn (User $user, string $id): bool => $user->id === $id);

Broadcast::channel('workspaces.{workspaceId}', function (User $user, string $workspaceId): bool {
    /** @var Workspace|null $workspace */
    $workspace = Workspace::query()->find($workspaceId);

    if ($workspace === null) {
        return false;
    }

    return $workspace->user_id === $user->id
        || $workspace->members()->whereKey($user->id)->exists();
});

Broadcast::channel('channels.{channelId}', function (User $user, string $channelId): array|false {
    /** @var Channel|null $channel */
    $channel = Channel::query()->with('workspace')->find($channelId);

    if ($channel === null) {
        return false;
    }

    $workspace = $channel->workspace;

    if ($workspace->user_id !== $user->id && ! $workspace->members()->whereKey($user->id)->exists()) {
        return false;
    }

    return ['id' => $user->id, 'name' => $user->name];
});
