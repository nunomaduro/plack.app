<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\WorkspaceTag;

final readonly class CreateWorkspaceTag
{
    public function handle(User $user, string $name): WorkspaceTag
    {
        return $user->workspaceTags()->create([
            'name' => $name,
        ]);
    }
}
