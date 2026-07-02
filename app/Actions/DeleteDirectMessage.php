<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\DirectMessage;

final readonly class DeleteDirectMessage
{
    public function handle(DirectMessage $message): void
    {
        $message->delete();
    }
}
