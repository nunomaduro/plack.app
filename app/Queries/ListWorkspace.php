<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

final readonly class ListWorkspace
{
    /**
     * All workspaces the user can access — the ones they own together with
     * the ones they were invited to and joined — for the workspace switcher.
     *
     * @return Collection<int, array{id: string, name: string, slug: string}>
     */
    public function get(User $user): Collection
    {
        $owned = $user->ownedWorkspaces()->get(['id', 'name', 'slug']);
        $member = $user->memberWorkspaces()->get(['workspaces.id', 'workspaces.name', 'workspaces.slug']);

        return $owned
            ->concat($member)
            ->unique('id')
            ->sortBy('name')
            ->map(fn (Workspace $workspace): array => [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'slug' => $workspace->slug,
            ])
            ->values();
    }
}
