<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Workspace;
use Inertia\Inertia;
use Inertia\Response;

final readonly class WorkspaceChannelController
{
    public function __invoke(Workspace $workspace): Response
    {
        return Inertia::render('workspace/channels', [
            'workspace' => $workspace,
        ]);
    }
}
