<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\WorkspaceInvitation;

final readonly class DeleteWorkspaceInvitation
{
    public function handle(WorkspaceInvitation $invitation): void
    {
        $invitation->delete();
    }
}
