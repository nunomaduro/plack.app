<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Collection;

final readonly class ParseMentions
{
    /**
     * @return Collection<int, User>
     */
    public function handle(string $body): Collection
    {
        if (preg_match_all('/(?<!\S)@([\w.-]+)/', $body, $matches) === 0) {
            return collect();
        }

        $usernames = collect($matches[1])->unique()->values();

        return User::query()
            ->whereIn('username', $usernames)
            ->get();
    }
}
