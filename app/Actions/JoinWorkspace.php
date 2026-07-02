<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workspace;

final readonly class JoinWorkspace
{
    public function handle(Workspace $workspace, User $user): void
    {
        $workspace->members()->syncWithoutDetaching([$user->id]);
    }
}
