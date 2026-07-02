<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Channel;
use App\Models\ChannelMember;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

final readonly class ListUnreadCounts
{
    private const int CAP = 100;

    /**
     * Unread message count per channel in the workspace, keyed by channel id.
     * A channel the user has never read counts all messages from other users.
     * Counting stops at {@see self::CAP} rows per channel so a large backlog
     * never scans more than that (render as "99+").
     *
     * @return Collection<string, int<0, max>>
     */
    public function get(User $user, Workspace $workspace): Collection
    {
        $channelIds = $workspace->channels()->get(['id'])->map(fn (Channel $channel): string => $channel->id);

        $lastReadAt = ChannelMember::query()
            ->where('user_id', $user->id)
            ->whereIn('channel_id', $channelIds)
            ->pluck('last_read_at', 'channel_id');

        return $channelIds->mapWithKeys(function (string $id) use ($user, $lastReadAt): array {
            $unread = Message::query()
                ->where('channel_id', $id)
                ->where('user_id', '!=', $user->id);

            $readAt = $lastReadAt->get($id);

            if ($readAt !== null) {
                $unread->where('created_at', '>', $readAt);
            }

            $capped = $unread->select('id')->limit(self::CAP);

            return [$id => Message::query()->fromSub($capped, 'capped')->count('id')];
        });
    }
}
