<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workspace;

final readonly class DeleteWorkspace
{
    public function handle(Workspace $workspace): void
    {
        $workspace->delete();
    }
}
