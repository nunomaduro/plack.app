<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Workspace;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class SearchMessages
{
    /**
     * @return LengthAwarePaginator<int, Message>
     */
    public function handle(Workspace $workspace, string $query, ?Channel $channel = null): LengthAwarePaginator
    {
        $results = Message::search($query)
            ->when($channel, fn ($search) => $search->where('channel_id', $channel->id))
            ->when(! $channel, fn ($search) => $search->whereIn('channel_id', $workspace->channels()->pluck('id')->all()))
            ->latest()
            ->paginate();

        $results->loadMissing(['sender', 'channel']);

        return $results;
    }
}
