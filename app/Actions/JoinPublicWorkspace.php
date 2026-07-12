<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Cache;

final readonly class JoinPublicWorkspace
{
    public function handle(Workspace $workspace, User $user): void
    {
        $workspace->members()->syncWithoutDetaching([$user->id]);

        Cache::forget("workspace:{$workspace->id}:mentionable_users");
    }
}
