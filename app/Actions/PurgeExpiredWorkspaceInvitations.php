<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\WorkspaceInvitation;

final readonly class PurgeExpiredWorkspaceInvitations
{
    public function handle(): void
    {
        WorkspaceInvitation::query()
            ->where('expires_at', '<', now())
            ->delete();
    }
}
