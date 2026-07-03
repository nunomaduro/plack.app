<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ChannelVisibility;
use App\Models\Channel;

final readonly class UpdateChannelVisibility
{
    public function handle(Channel $channel, ChannelVisibility $channelVisibility): void
    {
        $channel->update([
            'visibility' => $channelVisibility,
        ]);
    }
}
