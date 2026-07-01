<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workspace;

final readonly class UpdateWorkspace
{
    public function handle(User $user,int $id, string $name): Workspace
    {
        return $user->workspaces()->update(id,[
            'name' => $name,
        ]);
    }
}
