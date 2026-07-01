<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Message;

final readonly class DeleteMessage
{
    public function handle(Message $message): void
    {
        $message->delete();
    }
}
