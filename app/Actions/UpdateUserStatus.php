<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Carbon\CarbonInterface;

final readonly class UpdateUserStatus
{
    public function handle(User $user, ?string $emoji, ?string $text, ?CarbonInterface $expiresAt): void
    {
        $user->update([
            'status_emoji' => $emoji,
            'status_text' => $text,
            'status_expires_at' => $expiresAt,
        ]);
    }
}
