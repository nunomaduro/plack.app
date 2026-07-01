<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserTag;

final readonly class UpdateUserTag
{
    public function handle(UserTag $tag, string $name): void
    {
        $tag->update([
            'name' => $name,
        ]);
    }
}
