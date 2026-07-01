<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use App\Models\WorkspaceInvitation;
use Illuminate\Support\Collection;

final readonly class ListPendingWorkspaceInvitations
{
    /**
     * @return Collection<int, array{
     *     code: string,
     *     workspace: array{id: string, name: string},
     *     invitedBy: string,
     * }>
     */
    public function get(User $user): Collection
    {
        return WorkspaceInvitation::query()
            ->where('email', $user->email)
            ->where('expires_at', '>', now())
            ->with(['workspace', 'inviter'])
            ->latest()
            ->get()
            ->map(fn (WorkspaceInvitation $invitation): array => [
                'code' => $invitation->code,
                'workspace' => [
                    'id' => $invitation->workspace->id,
                    'name' => $invitation->workspace->name,
                ],
                'invitedBy' => $invitation->inviter->name,
            ]);
    }
}
