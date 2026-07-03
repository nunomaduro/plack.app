<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ChannelVisibility;
use App\Events\ChannelUpdated;
use App\Models\Channel;

final readonly class UpdateChannel
{
    public function handle(Channel $channel, string $name, ChannelVisibility $channelVisibility): void
    {
        $channel->update([
            'name' => $name,
            'visibility' => $channelVisibility,
        ]);

        broadcast(new ChannelUpdated($channel))->toOthers();
    }
}
