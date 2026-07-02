<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\WorkspaceInvitation;
use Illuminate\Support\Facades\DB;

final readonly class AcceptPrivateWorkspaceInvitation
{
    public function handle(WorkspaceInvitation $invitation, User $user): void
    {
        DB::transaction(function () use ($invitation, $user): void {
            $invitation->workspace->members()->syncWithoutDetaching([$user->id]);
            $invitation->delete();
        });
    }
}
