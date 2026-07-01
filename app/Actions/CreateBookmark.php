<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Bookmark;
use App\Models\Message;
use App\Models\User;

final readonly class CreateBookmark
{
    public function handle(User $user, Message $message): Bookmark
    {
        return Bookmark::query()->firstOrCreate([
            'user_id' => $user->id,
            'message_id' => $message->id,
        ]);
    }
}
