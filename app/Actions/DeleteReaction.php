<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Reaction;

final readonly class DeleteReaction
{
    public function handle(Reaction $reaction): void
    {
        $reaction->delete();
    }
}
