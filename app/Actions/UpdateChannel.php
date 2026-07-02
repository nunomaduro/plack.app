<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use Illuminate\Support\Str;

final readonly class UpdateChannel
{
    public function handle(Channel $channel, string $name, ?string $slug = null): void
    {
        $channel->update([
            'name' => $name,
            'slug' => $slug ?? Str::slug($name),
        ]);
    }
}
