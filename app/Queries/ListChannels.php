<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final readonly class ListChannels
{
    /**
     * The workspace's channels for the sidebar, each carrying the user's
     * unread message count and mute state.
     *
     * @return Collection<int, array{id: string, name: string, slug: string, unread_count: int<0, max>, muted: bool}>
     */
    public function get(User $user, Workspace $workspace): Collection
    {
        $channels = $workspace->channels()->latest()->get();

        $metadata = $user->userChannelMetadata()
            ->whereIn('channel_id', $channels->modelKeys())
            ->get();

        $lastReadAt = [];
        $mutedAt = [];

        foreach ($metadata as $entry) {
            $lastReadAt[$entry->channel_id] = $entry->last_read_at;
            $mutedAt[$entry->channel_id] = $entry->muted_at;
        }

        $joinedAt = $this->joinedAt($user, $workspace);

        return $channels->map(function (Channel $channel) use ($user, $lastReadAt, $mutedAt, $joinedAt): array {
            // Count from the last read marker, or from when the user first
            // gained access to the workspace if they never opened the channel.
            $readSince = $lastReadAt[$channel->id] ?? $joinedAt;

            $unreadCount = $channel->messages()
                ->where('user_id', '!=', $user->id)
                ->where('created_at', '>', $readSince)
                ->count();

            return [
                'id' => $channel->id,
                'name' => $channel->name,
                'slug' => $channel->slug,
                'unread_count' => $unreadCount,
                'muted' => ($mutedAt[$channel->id] ?? null) !== null,
            ];
        });
    }

    /**
     * When the user gained access to the workspace: their membership date for
     * members, or the workspace's creation date for the owner.
     */
    private function joinedAt(User $user, Workspace $workspace): CarbonInterface
    {
        $memberSince = DB::table('workspace_user')
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $user->id)
            ->value('created_at');

        if (is_string($memberSince)) {
            return Carbon::parse($memberSince);
        }

        return $workspace->created_at;
    }
}
