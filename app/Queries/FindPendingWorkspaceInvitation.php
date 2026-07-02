<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\WorkspaceInvitation;

final readonly class FindPendingWorkspaceInvitation
{
    /**
     * @return array{code: string, workspace: string, memberCount: int}|null
     */
    public function get(mixed $code): ?array
    {
        if (! is_string($code) || $code === '') {
            return null;
        }

        $invitation = WorkspaceInvitation::query()
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->with('workspace')
            ->first();

        if ($invitation === null) {
            return null;
        }

        return [
            'code' => $invitation->code,
            'workspace' => $invitation->workspace->name,
            'memberCount' => $invitation->workspace->memberCount(),
        ];
    }
}
