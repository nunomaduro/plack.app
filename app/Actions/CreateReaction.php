<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Emoji;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final readonly class CreateReaction
{
    public function handle(User $user, Model $reactable, Emoji $emoji): Reaction
    {
        return $reactable->reactions()->firstOrCreate([
            'user_id' => $user->id,
            'emoji' => $emoji->value,
        ]);
    }
}
