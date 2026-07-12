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

        $names = collect($matches[1])
            ->map(fn (string $name): string => str_replace('.', ' ', $name))
            ->unique()
            ->values();

        return User::query()
            ->whereIn('name', $names)
            ->get();
    }
}
