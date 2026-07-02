<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;
use Inertia\Response;

final readonly class WorkspaceChannelController
{
    public function __invoke(Workspace $workspace): Response
    {
        return Inertia::render('workspace/channels', [
            'workspace' => $workspace->load(['channels' => fn (HasMany $channels) => $channels->latest()]),
        ]);
    }
}
