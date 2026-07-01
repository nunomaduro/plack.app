<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserTag;
use App\Models\Workspace;

final readonly class CreateUserTag
{
    public function handle(Workspace $workspace, string $name): UserTag
    {
        return $workspace->userTags()->create([
            'name' => $name,
        ]);
    }
}
