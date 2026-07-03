<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;

final readonly class UpdateChannel
{
    public function handle(Channel $channel, string $name): Channel
    {
        $channel->update([
            'name' => $name,
        ]);

        return $channel->refresh();
    }
}
