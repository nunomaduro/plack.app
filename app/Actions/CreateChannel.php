<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ChannelVisibility;
use App\Models\Channel;
use App\Models\Workspace;

final readonly class CreateChannel
{
    public function handle(Workspace $workspace, string $name, ChannelVisibility $channelVisibility = ChannelVisibility::Public): Channel
    {
        return $workspace->channels()->create([
            'name' => $name,
            'visibility' => $channelVisibility,
        ]);
    }
}
