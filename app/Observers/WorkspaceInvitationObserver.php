<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\WorkspaceInvitation;
use Illuminate\Support\Str;

final class WorkspaceInvitationObserver
{
    public function creating(WorkspaceInvitation $invitation): void
    {
        $invitation->code ??= Str::random(64);
    }
}
