<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workspace;

final readonly class UpdateWorkspace
{
    public function handle(Workspace $workspace, string $name): void
    {
        $workspace->update([
            'name' => $name,
        ]);
    }
}
