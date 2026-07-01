<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workspace;

final readonly class CreateWorkspace
{
    public function handle(User $user, string $name): Workspace
    {
        return $user->ownedWorkspaces()->create([
            'name' => $name,
        ]);
    }
}
