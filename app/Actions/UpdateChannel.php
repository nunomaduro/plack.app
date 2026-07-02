<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\ChannelUpdated;
use App\Models\Channel;

final readonly class UpdateChannel
{
    public function handle(Channel $channel, string $name): void
    {
        $channel->update([
            'name' => $name,
        ]);

        broadcast(new ChannelUpdated($channel))->toOthers();
    }
}
