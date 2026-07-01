<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\StatusPreset;
use App\Models\User;

final readonly class CreateStatusPreset
{
    public function handle(User $user, string $emoji, string $text): StatusPreset
    {
        return $user->statusPresets()->create([
            'emoji' => $emoji,
            'text' => $text,
        ]);
    }
}
